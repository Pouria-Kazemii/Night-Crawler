<?php

namespace App\Console\Commands;

use App\Jobs\ProcessSendingCrawlerJob;
use App\Models\CrawlerJobSender;
use App\Models\CrawlerNode;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

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
        $jobs = CrawlerJobSender::where(function ($query) {
            $query->whereHas('lastResult', function ($q) {
                $q->where('updated_at', '<=', Carbon::now('UTC')->addMinutes(-1));
            })
                ->orWhereDoesntHave('crawlerResults');
        })
            ->where('status', 'running')
            ->where('last_used_at', '<=', Carbon::now('UTC')->addMinutes(-1))
            ->with('crawler')
            ->get();

        if (count($jobs) > 0) {

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

                foreach ($jobs as $job) {

                    $newNode = $sortedNodes->firstWhere('_id', '!=', $job->node_id);

                    $changed = true;

                    $retries = $job->retries + 1;

                    $update['retries'] = $retries;

                    if ($newNode == null) {
                        $changed = false;
                        $newNode = $sortedNodes->first();
                    }

                    if ($changed) {
                        $job->load('crawlerResults')->delete();

                        $update['node_id'] = $newNode->id;
                    }

                    Cache::forget($job->_id . '_status');
                    Cache::forget($job->_id . '_failed_urls');
                    Cache::forget($job->_id . '_success_count');
                    Cache::forget($job->_id . '_repeated_count');
                    Cache::forget($job->_id . '_changed_count');
                    Cache::forget($job->_id . '_new_count');

                    if (($newNode->active_jobs_count === 0 and $changed) || ($newNode->active_jobs_count === 1 and !$changed)) {

                        dispatch(new ProcessSendingCrawlerJob($job))->onConnection('crawler-send');
                    } else {

                        $update['status'] = 'queued';
                    }
                }

                $job->update($update);
            }
        }
    }
}
