<?php

namespace App\Jobs;

use App\Models\Crawler;
use App\Models\CrawlerJobSender;
use App\Models\CrawlerResult;
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
            $content = $validated['type'] === 'seed'
                ? array_map('urldecode', $validated['content'] ?? [])
                : $validated['content'];

            $existing = CrawlerResult::where('encrypt_url', hash('sha256', $validated['final_url']))->first();

            if ($existing) {

                $contentChanged = $existing->content !== $content;

                if ($contentChanged) {

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

        // ❌ Failed result
        else {
            $failedKey = $jobId . '_failed_urls';
            $statusKey = $jobId . '_status';

            $failedUrls = Cache::get($failedKey, []);
            $failedUrls[] = [
                $validated['original_url'] => [
                    'error' => $validated['error'],
                    'status_code' => $validated['status_code']
                ]
            ];
            Cache::forever($failedKey, $failedUrls);
            Cache::forever($statusKey, 'failed');
        }

        // ✅ Final response check
        if ($validated['is_last']) {

            $crawler = Crawler::withQueuedOrRunningSender()->find($crawlerId);

            $statusKey = $jobId . '_status';
            $failedKey = $jobId . '_failed_urls';
            $jobStatus = Cache::get($statusKey) ?? 'success';

            $update = ['status' => $jobStatus];
            if ($jobStatus === 'failed') {
                $update['failed_url'] = Cache::get($failedKey, []);
            }

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
                        $crawlerStatus = 'first_step_done';
                        app(\App\Services\CreateNodeRequest::class)->goSecondStep($crawlerId);
                    } else if ($crawler->schedule != null) {
                        $crawlerStatus = 'active';
                    } else {
                        $crawlerStatus = 'completed';
                    }
                } else {
                    $crawlerStatus = 'error';
                }

                $crawler->update(['crawler_status' => $crawlerStatus]);
            }
        }
    }
}
