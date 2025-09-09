<?php

namespace App\Console\Commands;

use App\Models\Crawler;
use App\Services\CreateNodeRequest;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckCrawlerSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-crawler-schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check schedule of every crawler';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $scheduleCrawlers = Crawler::
        where(function ($query) {
            $query->whereDoesntHave('crawlerJobSender', function ($q) {
                $q->where('status', 'queued');
            });
        })->
        whereNotNull('next_run_at')->
        where('schedule', '!=', '0')->
        where('schedule' , '!=' , null)->
        where('crawler_status', '!=', 'pause')->
        where('next_run_at', '<=', Carbon::now('UTC'))->
        get();

        foreach ($scheduleCrawlers as $scheduleCrawler) {

            $crawlerManager = app(CreateNodeRequest::class);

            $crawlerManager->go($scheduleCrawler);

            $this->info("Crawler {$scheduleCrawler->title} run for schedule");
        }
    }
}
