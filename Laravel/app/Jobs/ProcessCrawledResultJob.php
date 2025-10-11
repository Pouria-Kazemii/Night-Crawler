<?php

namespace App\Jobs;

use App\Models\Crawler;
use App\Models\CrawlerJobSender;
use App\Models\CrawlerResult;
use App\Providers\CountManagementServiceProvider;
use App\Services\CountManagement\DTOs\CounterData;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

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
        $type = $this->data['type'];
        $originalUrl = $this->data['original_url'];
        $isLast = $this->data['is_last'];
        $statusCode = $this->data['status_code'];
        $crawlerId = $this->data['meta']['crawler_id'];
        $jobId = $this->data['meta']['job_id'];
        $finalUrl = $this->data['final_url'] ?? null;
        $content = $this->data['content'] ?? null;
        $error = $this->data['error'] ?? null;
        $firstStep = $this->data['first_step'] ?? false;


        $jobInstance = CrawlerJobSender::find($jobId);

        $resultCount = CountManagementServiceProvider::createForJob($jobId, $jobInstance->counts);

        // ✅ Handle successful result
        if ($statusCode === 200) {

            $resultCount->incrementSuccess();

            $newContent = $this->fixedContent($content, $type, $firstStep);

            $existingResult = $this->checkContentExists($finalUrl);

            $urlCondition = ($type === 'seed' or $firstStep);

            if ($urlCondition) {

                if (!is_null($existingResult)) {

                    $oldContent = $existingResult->content;

                    $resultCount->incrementRepeated();

                    $contentChanged = $oldContent !== $newContent;

                    if ($contentChanged) {

                        $contentDifference = array_values(array_diff($newContent, $oldContent));

                        $resultCount->incrementsChanged(count($contentDifference));

                        $resultCount->incrementsNotChanged(count($oldContent) - count($contentDifference));

                        $existingResult->update([
                            'content_difference' => $contentDifference,
                            'crawler_job_sender_id' => $jobId,
                            'content' => $newContent,
                            'content_upgrade' => true,
                        ]);
                    } else {
                        $resultCount->incrementsNotChanged(count($newContent));

                        $existingResult->update([
                            'crawler_job_sender_id' => $jobId,
                            'content_difference' => [],
                            'content_upgrade' => false
                        ]);
                    }
                } else {
                    CrawlerResult::create([
                        'encrypt_url' => hash('sha256', $finalUrl),
                        'crawler_id' => $crawlerId,
                        'final_url' => urldecode($finalUrl),
                        'url' => $originalUrl,
                        'crawler_job_sender_id' => $jobId,
                        'content' => $content,
                        'content_upgrade' => true,
                        'content_difference' => null
                    ]);
                }
            } else {
                if (!is_null($existingResult)) {

                    $oldUpdateContent = $existingResult->content_difference;

                    $resultCount->incrementRepeated();

                    $contentChanged = $oldUpdateContent !== $newContent;

                    if ($contentChanged) {

                        $resultCount->incrementChanged();

                        $existingResult->update([
                            'crawler_job_sender_id' => $jobId,
                            'content_update' => true,
                            'content_difference' => $newContent
                        ]);
                    } else {

                        $resultCount->incrementNotChanged();

                        $existingResult->update([
                            'crawler_job_sender_id' => $jobId,
                            'content_update' => false,
                        ]);
                    }
                } else {
                    CrawlerResult::create([
                        'encrypt_url' => hash('sha256', $finalUrl),
                        'crawler_id' => $crawlerId,
                        'final_url' => urldecode($finalUrl),
                        'url' => $originalUrl,
                        'crawler_job_sender_id' => $jobId,
                        'content' => $content,
                        'content_update' => true,
                        'content_difference' => null
                    ]);
                }
            }
        } else {
            $resultCount->markAsFailed();

            $resultCount->addFailedUrl(
                url: $originalUrl,
                error: $error,
                statusCode: $statusCode
            );
        }

        // ✅ Final response check
        if ($isLast) {

            $finalData = $resultCount->getAllData();

            $this->saveResults($finalData, $jobInstance);

            $resultCount->cleanup();

            $crawlerInstance = Crawler::withQueuedOrRunningSender()->find($crawlerId);

            $newSender = CrawlerJobSender::getLastQueued($jobInstance->node_id);

            if ($newSender->exists()) {

                dispatch(new ProcessSendingCrawlerJob($newSender->first()))->onConnection('crawler-send');
            }

            // If this was the last running job
            if ($crawlerInstance && count($crawlerInstance->crawlerJobSender) === 1) {

                $allJobs = Crawler::withNotProcessedSender()->find($crawlerId)?->crawlerJobSender ?? [];

                $allSuccess = true;

                foreach ($allJobs as $job) {

                    $job->update(['processed' => true]);

                    if ($job->status == 'failed') {
                        $allSuccess = false;
                    }
                }

                if ($allSuccess) {

                    $step = $allJobs->last()->step;

                    if ($step === 1) {

                        $crawlerUpdate = [
                            'crawler_status' => 'first_step_done'
                        ];

                        app(\App\Services\CreateNodeRequest::class)->goSecondStep($crawlerInstance->_id, false , $allJobs?->pluck('id'));
                    } else {

                        $haveSchedule = false;

                        //TODO :  FIX UPDATE ADN UPGRADE SCHEDULE
//                        if (!empty($crawlerInstance->schedule['update']) and $crawlerInstance->schedule['update'] > 0 ) {
//                            $crawlerUpdate = [
//                                'crawler_status' => 'active',
//                                'next_update_run_at' => Carbon::now('UTC')->addMinutes((int)$crawlerInstance->schedule['update'])
//                            ];
//                            $haveSchedule = true;
//                        }
//
//                        if (!empty($crawlerInstance->schedule['upgrade']) and $crawlerInstance->schedule['upgrade'] > 0) {
//                            $crawlerUpdate = [
//                                'crawler_status' => 'active',
//                                'next_upgrade_run_at' => Carbon::now('UTC')->addMinutes((int)$crawlerInstance->schedule['upgrade'])
//                            ];
//                            $haveSchedule = true;
//                        }

                        if(!$haveSchedule) {

                            $crawlerUpdate = [
                                'crawler_status' => 'completed'
                            ];
                        }
                    }

                    $crawlerInstance->update($crawlerUpdate);
                }
            }
        }
    }

    private function saveResults(CounterData $data, CrawlerJobSender $jobInstance): void
    {
        $jobInstance->update([
            'status' => $data->status->value,
            'counts' => [
                'url' => $jobInstance->counts['url'],
                'success'  => $data->counts['success'],
                'repeated' => $data->counts['repeated'],
                'changed'  => $data->counts['changed'],
                'not_changed' => $data->counts['not_changed'],
            ],
            'failed_url' => $data['failedUrls']
        ]);
    }

    private function fixedContent(array $content, string $type, bool $firstStep): array
    {
        if ($type === 'seed' or $firstStep) {
            return array_map('urldecode', $content ?? []);
        } else {
            return $content;
        }
    }

    private function checkContentExists(string $finalUrl): mixed
    {
        return CrawlerResult::where('encrypt_url', hash('sha256', $finalUrl))->first();
    }
}
