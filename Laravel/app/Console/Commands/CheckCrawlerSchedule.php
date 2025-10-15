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
        $scheduleUpdateCrawlers = Crawler::where(function ($query) {
            $query->whereDoesntHave('crawlerJobSender', function ($q) {
                $q->where('status', '!=', 'success');
            });
        })->whereNotNull('next_update_run_at')
        ->where('crawler_status', '!=', 'pause')
        ->where('next_update_run_at', '<=', Carbon::now('UTC'))
        ->orderBy('crawler_priority')->get();

        $scheduleUpgradeCrawlers = Crawler::where(function ($query) {
            $query->whereDoesntHave('crawlerJobSender', function ($q) {
                $q->where('status', '!=', 'success');
            });
        })->whereNotNull('next_upgrade_run_at')
        ->where('crawler_status', '!=', 'pause')
        ->where('next_upgrade_run_at', '<=', Carbon::now('UTC'))
        ->orderBy('crawler_priority')->get();

        foreach ($scheduleUpdateCrawlers as $scheduleUpdateCrawler) {

            if (($scheduleUpdateCrawler->schedule['update'] ?? 0) != 0) {

                $crawlerManager = app(CreateNodeRequest::class);

                $crawlerManager->go($scheduleUpdateCrawler, true);
            } else {
                
                $scheduleUpdateCrawler->update([
                    'next_update_run_at' => 0
                ]);
            }
        }

        foreach ($scheduleUpgradeCrawlers as $scheduleUpgradeCrawler) {

            if (($scheduleUpdateCrawler->schedule['upgrade'] ?? 0) != 0) {

                $crawlerManager = app(CreateNodeRequest::class);

                $crawlerManager->go($scheduleUpgradeCrawler, false);
            } else {
                $scheduleUpgradeCrawler->update([
                    'next_upgrade_run_at' => 0
                ]);
            }
        }
    }
}
