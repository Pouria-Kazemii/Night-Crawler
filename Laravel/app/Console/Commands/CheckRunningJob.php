<?php

namespace App\Console\Commands;

use App\Jobs\ProcessSendingCrawlerJob;
use App\Models\Crawler;
use App\Models\CrawlerJobSender;
use App\Models\CrawlerNode;
use App\Models\CrawlerResult;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckRunningJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-running-job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '
        This command check the jobs with more than one minutes taken time. 
        One minute past from last result inserted or One minute when it start
        and still Have not any result. ';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $jobs = CrawlerJobSender::where('status', 'running')
            ->where('last_used_at', '<=', Carbon::now('UTC')->addMinutes(-10))
            ->with('crawler')
            ->get();

        // Pre-load latest results for all jobs
        $jobIds = $jobs->pluck('id');
        $latestResults = CrawlerResult::whereIn('crawler_job_sender_id', $jobIds)
            ->orderBy('updated_at', 'desc')
            ->get()
            ->groupBy('crawler_job_sender_id')
            ->map(function ($results) {
                return $results->first(); // Get latest result for each job
            });

        // Filter jobs
        $filteredJobs = $jobs->filter(function ($job) use ($latestResults) {
            $latestResult = $latestResults->get($job->id);
            return is_null($latestResult) ||
                $latestResult->updated_at <= Carbon::now('UTC')->addMinutes(-10);
        });

        if (count($filteredJobs) > 0) {

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

                foreach ($filteredJobs as $job) {

                    $newNode = $sortedNodes->firstWhere('_id', '!=', $job->node_id);

                    $changed = true;

                    $retries = $job->retries + 1;

                    $update['retries'] = $retries;

                    $job->load(['crawlerResults' => function ($query) {
                        $query->select('id', 'url', 'crawler_job_sender_id');
                    }]);

                    $gettedUrls = $job->crawlerResults->pluck('url')->toArray();

                    $remainingUrls = $job->urls;

                    $newURls = array_diff($remainingUrls, $gettedUrls);

                    $job->update(['urls' => $newURls]);

                    if ($newNode == null) {
                        $changed = false;
                        $newNode = $sortedNodes->first();
                    }

                    if ($changed) {

                        $update['node_id'] = $newNode->id;
                    }

                    if ($newNode->active_jobs_count === 0) {

                        dispatch(new ProcessSendingCrawlerJob($job))->onConnection('crawler-send')->onQueue('crawler-send-jobs');
                    } else {

                        $update['status'] = 'queued';
                    }

                    $job->update($update);
                }
            }
        }
    }
}
