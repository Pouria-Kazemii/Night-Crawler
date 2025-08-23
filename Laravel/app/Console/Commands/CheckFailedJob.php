<?php

namespace App\Console\Commands;

use App\Models\CrawlerJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

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
        $failedJobs = CrawlerJob::where('status' , 'failed')->get();
        
        if(count($failedJobs) == 0){
            return Log::alert('There is no failed job' , ['app\\Console\\Commandd\\CheckFailedJob.php']);
        }

        foreach($failedJobs->get() as $failedJob){
            dd($failedJob->failed);
            $failedJob->failedUrl;
        }
    }
}
