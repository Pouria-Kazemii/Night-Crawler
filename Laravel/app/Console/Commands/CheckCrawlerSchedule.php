<?php

namespace App\Console\Commands;

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
        //
    }
}
