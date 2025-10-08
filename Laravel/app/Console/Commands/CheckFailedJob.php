<?php

namespace App\Console\Commands;

use App\Jobs\ProcessSendingCrawlerJob;
use App\Models\CrawlerJobSender;
use App\Models\CrawlerNode;
use Illuminate\Console\Command;

class CheckFailedJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-failed-job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retry to run failed jobs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $failedJobs = CrawlerJobSender::where('status', 'failed')->where('retries' , '<=' , 3)->get();

        if (count($failedJobs) != 0) {

            $sortedNodes = CrawlerNode::sortedActive()
                ->get()
                ->map(function ($node) {
                    $node->active_jobs_count = $node->crawlerJobSender()
                        ->whereIn('status', ['running', 'queued'])
                        ->count();
                    return $node;
                })
                ->sortBy(function ($node) {
                    return [$node->active_jobs_count, $node->latency];
                })
                ->values();

            if ($sortedNodes->count() > 0) {

                foreach ($failedJobs as $job) {

                    $currentCrawlDelay = data_get($job->payload, 'options.crawl_delay', 1);

                    $retries = $job->retries + 1;

                    $newUrls = collect($job->failed_url)->pluck('url')->toArray();

                    $newNode = $sortedNodes->firstWhere('_id', '!=', $job->node_id);

                    $newDelay = $currentCrawlDelay + $retries;

                    $update['urls'] = $newUrls;
                    $update['retries'] = $retries;
                    $update['payload.options.crawl_delay'] = (int) $newDelay;

                    $changed = true;

                    if ($newNode == null) {
                        $changed = false;
                        $newNode = $sortedNodes->first();
                    }

                    if ($changed) {

                        $update['node_id'] = $newNode->id;
                    }

                    if ($newNode->active_jobs_count === 0) {

                        dispatch(new ProcessSendingCrawlerJob($job))->onConnection('crawler-send');
                    } else {
                        $update['status'] = 'queued';
                    }

                    $update['failed_url'] = [] ;

                    $job->update($update);

                    $job->load('crawler')->update(['crawl_delay' => $newDelay]);
                }
            }
        }
    }
}
