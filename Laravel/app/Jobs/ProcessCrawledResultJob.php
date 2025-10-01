<?php

namespace App\Jobs;

use App\Models\Crawler;
use App\Models\CrawlerJobSender;
use App\Models\CrawlerResult;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProcessCrawledResultJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function handle(): void
    {
        $validated = Validator::make($this->data, [
            'type' => ['required', Rule::in(['dynamic', 'seed', 'static', 'paginated', 'api', 'authenticated'])],
            'original_url' => 'required',
            'final_url' => 'nullable',
            'content' => 'nullable',
            'error' => 'nullable',
            'is_last' => 'required|boolean',
            'status_code' => 'required|integer',
            'meta' => 'required|array',
        ])->validate();

        $jobId = $validated['meta']['job_id'];
        $crawlerId = $validated['meta']['crawler_id'];

        // ✅ Handle successful result
        if ($validated['status_code'] === 200) {

            $successKey = $jobId . '_success_count';
            $successCount = Cache::get($successKey, 0);
            $successCount = $successCount + 1;
            Cache::forever($successKey, $successCount);

            $content = $validated['type'] === 'seed'
                ? array_map('urldecode', $validated['content'] ?? [])
                : $validated['content'];

            $existing = CrawlerResult::where('encrypt_url', hash('sha256', $validated['final_url']))
                ->where('crawler_id', $crawlerId)
                ->first();

            if ($existing) {

                $repeatedKey = $jobId . '_repeated_count';
                $repeatedCount = Cache::get($repeatedKey, 0);
                $repeatedCount = $repeatedCount + 1;
                Cache::forever($repeatedKey, $repeatedCount);

                $contentChanged = $existing->content !== $content;

                if ($contentChanged) {

                    $changedKey = $jobId . '_changed_count';
                    $changedCount = Cache::get($changedKey, 0);
                    $changedCount = $changedCount + 1;
                    Cache::forever($changedKey, $changedCount);


                    $updateData = [
                        'crawler_job_sender_id' => $jobId,
                        'content' => $content,
                        'content_changed' => true
                    ];

                    if ($validated['type'] === 'seed') {
                        $dif = array_values(array_diff($content, $existing->content));
                        $updateData['content_dif'] = $dif;
                    }

                    $existing->update($updateData);
                } else {
                    $existing->update([
                        'crawler_job_sender_id' => $jobId,
                        'content_changed' => false,
                    ]);
                }
            } else {

                $newKey = $jobId . '_new_count';
                $newCount = Cache::get($newKey, 0);
                $newCount = $newCount + 1;
                Cache::forever($newKey, $newCount);

                CrawlerResult::create([
                    'encrypt_url' => hash('sha256', $validated['final_url']),
                    'crawler_id' => $crawlerId,
                    'final_url' => urldecode($validated['final_url']),
                    'url' => $validated['original_url'],
                    'crawler_job_sender_id' => $jobId,
                    'content' => $content,
                    'content_changed' => true
                ]);
            }
        }

        // Failed result
        else {
            $failedKey = $jobId . '_failed_urls';
            $statusKey = $jobId . '_status';

            $failedUrls = Cache::get($failedKey, []);
            $failedUrls[] = [
                'url' => $validated['original_url'],
                'error' => $validated['error'],
                'status_code' => $validated['status_code']

            ];
            Cache::forever($failedKey, $failedUrls);
            Cache::forever($statusKey, 'failed');
        }

        // ✅ Final response check
        if ($validated['is_last']) {

            $crawler = Crawler::withQueuedOrRunningSender()->find($crawlerId);

            $statusKey = $jobId . '_status';
            $failedKey = $jobId . '_failed_urls';
            $successKey = $jobId . '_success_count';
            $repeatedKey = $jobId . '_repeated_count';
            $changedKey = $jobId . '_changed_count';
            $newKey = $jobId . '_new_count';

            $jobStatus = Cache::get($statusKey) ?? 'success';
            //if()
            $update = [
                'status' => $jobStatus,
                'counts' => [
                    'success'  => Cache::get($successKey, 0),
                    'repeated' => Cache::get($repeatedKey, 0),
                    'changed'  => Cache::get($changedKey, 0),
                    'new'      => Cache::get($newKey, 0)
                ]
            ];

            if ($jobStatus === 'failed') {
                $update['failed_url'] = Cache::get($failedKey, []);
            }

            Cache::forget($successKey);
            Cache::forget($repeatedKey);
            Cache::forget($changedKey);
            Cache::forget($newKey);

            $jobSender = CrawlerJobSender::find($jobId);

            $jobSender->update($update);

            $newSender = CrawlerJobSender::getLastQueued($jobSender->node_id);

            if ($newSender->exists()) {

                dispatch(new ProcessSendingCrawlerJob($newSender->first()))->onConnection('crawler-send');
            }

            // If this was the last running job
            if ($crawler && count($crawler->crawlerJobSender) === 1) {

                $allJobs = Crawler::withNotProcessedSender()->find($crawlerId)?->crawlerJobSender ?? [];

                $allSuccess = true;

                $step = $jobSender->step;

                foreach ($allJobs as $job) {

                    $job->update(['processed' => true]);

                    if (Cache::get($job->_id . '_status') === 'failed') {
                        $allSuccess = false;
                    }

                    Cache::forget($job->_id . '_status');
                    Cache::forget($job->_id . '_failed_urls');
                }

                if ($allSuccess) {

                    if ($step === 1) {

                        $crawlerUpdate = [
                            'crawler_status' => 'first_step_done'
                        ];
                        app(\App\Services\CreateNodeRequest::class)->goSecondStep($crawler->_id , 0 ,$allJobs?->pluck('id'));
                    } else if ($crawler->schedule != null) {

                        $crawlerUpdate = [
                            'crawler_status' => 'active',
                            'next_run_at' => Carbon::now('UTC')->addMinutes((int)$crawler->schedule)
                        ];
                    } else {

                        $crawlerUpdate = [
                            'crawler_status' => 'completed'
                        ];
                    }
                } else {

                    $crawlerUpdate = [
                        'crawler_status' => 'error'
                    ];
                }

                $crawler->update($crawlerUpdate);
            }
        }
    }
}
