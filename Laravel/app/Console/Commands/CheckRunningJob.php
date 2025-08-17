<?php

namespace App\Console\Commands;

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
    protected $description = 'This command check the jobs with more than 20 minutes taken time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
    }
}
