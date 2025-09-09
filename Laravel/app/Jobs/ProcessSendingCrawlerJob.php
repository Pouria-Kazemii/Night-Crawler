<?php

namespace App\Jobs;

use App\Models\CrawlerJobSender;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Http;

class ProcessSendingCrawlerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected CrawlerJobSender $crawler_job_sender;

    /**
     * Create a new job instance.
     */
    public function __construct($crawler_job_sender)
    {
        $this->crawler_job_sender = $crawler_job_sender;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {

            $this->crawler_job_sender->load('crawler')->crawler->update([
                'crawler_status' => 'running',
                'last_run_at' => now()
            ]);

            $this->crawler_job_sender->update([
                'status' => 'running',
                'last_used_at'  => now()
            ]);

            $payloadOptions = $this->crawler_job_sender->payload;

            $payloadOptions['urls'] = $this->crawler_job_sender->urls;

            $payloadOptions['meta'] = [
                'job_id' => $this->crawler_job_sender->_id,
                'crawler_id' => $this->crawler_job_sender->crawler_id
            ];

            $node = $this->crawler_job_sender->crawlerNode;

            $response = Http::timeout(15)->withHeaders([
                'Authorization' => 'Bearer ' . config('crawler.api_token'),
                'Accept' => 'application/json',
            ])->post("http://{$node->ip_address}:{$node->port}/crawl", $payloadOptions);


            if ($response->failed()) {

                $this->crawler_job_sender->update(['status' => 'failed']);

                $this->crawler_job_sender->crawler->update(['crawler_status' => 'failed']);

                $newSender = CrawlerJobSender::getLastQueued($node->_id);

                if ($newSender->exists()) {

                    dispatch(new ProcessSendingCrawlerJob($newSender->first()))->onConnection('crawler-send');
                }
            }
        } catch (\Exception $e) {

            $this->crawler_job_sender->update(['status' => 'failed']);

            $this->crawler_job_sender->crawler->update(['crawler_status' => 'failed']);

            $newSender = CrawlerJobSender::getLastQueued($node->_id);

            if ($newSender->exists()) {

                dispatch(new ProcessSendingCrawlerJob($newSender->first()))->onConnection('crawler-send');
            }
        }
    }
}
