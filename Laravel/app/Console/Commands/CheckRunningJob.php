<?php

namespace App\Console\Commands;

use App\Models\CrawlerJobSender;
use App\Models\CrawlerNode;
use App\Models\CrawlerResult;
use App\Services\CreateNodeRequest;
use Carbon\Carbon;
use GuzzleHttp\Promise\Create;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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
    protected $description = 'This command check the jobs with more than 20 minutes taken time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //TODO : TEST
        $jobs = CrawlerJobSender::where(function ($query) {
            $query->whereHas('lastResult', function ($q) {
                $q->where('updated_at', '<=', Carbon::now('UTC')->addMinutes(-1));
            })
                ->orWhereDoesntHave('crawlerResults');
        })
            ->where('status', 'running')
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

            foreach ($jobs as $job) {

                $newNode = $sortedNodes->where('_id', '!=', $job->node_id)->value('_id');

                $retries = $job->retries + 1;

                $job->update([
                    'node_id' => $newNode,
                    'retries' => $retries,
                    'status' => 'queued'
                ]);
            }
        }
    }
}
