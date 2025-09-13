<?php

namespace App\Services;

use App\Contracts\CreateNodeRequestInterface;
use App\Jobs\ProcessSendingCrawlerJob;
use App\Models\CrawlerNode;
use App\Models\Crawler;
use App\Models\CrawlerJobSender;
use App\Models\CrawlerResult;
use Illuminate\Support\Arr;

class CreateNodeRequest implements CreateNodeRequestInterface
{

    public function go(Crawler $crawler, int $retries = 0)
    {
        $urls = getUrls($crawler);

        $crawlerId = (string)$crawler->_id;

        $crawler->crawler_type != 'two_step' ? $payloadOptions = getOptions($crawler) :
            $payloadOptions = getOptions($crawler, $crawler->two_step['first']);

        $step = $crawler->crawler_type == 'two_step' ? 1 : 0;

        return $this->sendRequest($crawler, $urls, $payloadOptions, $crawlerId, $step);
    }

    public function goSecondStep(string $crawlerId, int $retries = 0, $jobs_id = null)
    {
        $results = CrawlerResult::whereIn('crawler_job_sender_id', $jobs_id)
            ->where('content_changed', true)
            ->get();

        $crawler = Crawler::find($crawlerId);

        if ($results != null and count($results) > 0) {

            foreach ($results as $arr) {
                $urls[] = isset($arr->content_dif)
                    ? $arr->content_dif
                    : $arr->content;
            }

            $urls = Arr::flatten($urls);

            $payloadOptions = getOptions($crawler, $crawler->two_step['second']);

            $this->sendRequest($crawler, $urls, $payloadOptions, $crawler->_id, 2);
        } else {
            $crawler->schedule == null ?
                $crawler->update([
                    'status' => 'completed',
                ]) :
                $crawler->update([
                    'status' => 'active',
                ]);
        }
    }


    public function sendRequest(Crawler $crawler, array $urls, array $payloadOptions, string $crawlerId, int $step, int $retries = 0)
    {
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


        if ($sortedNodes->isEmpty()) {
            return [
                'key' => 'error',
                'message' => 'هیج پروکسی فعالی وجود ندارد'
            ];
        }

        // 2. Split URLs into small chunks (e.g., 10 per chunk)
        $chunks = collect($urls)->chunk(ceil(count($urls) / $sortedNodes->count()))->values();

        foreach ($chunks as $index => $urlChunk) {
            // 3. Assign node in round-robin fashion
            $node = $sortedNodes[$index % $sortedNodes->count()];

            // 4. Create crawler_jobs record
            $sender = CrawlerJobSender::create([
                'crawler_id'    => $crawlerId,
                'node_id'       => $node->_id,
                'urls'          => $urlChunk->values()->all(),
                'status'        => 'queued',
                'retries'       => $retries,
                'step'          => $step,
                'payload'       => $payloadOptions,
                'processed'     => false,
                'started_at'    => now(),
            ]);

            if (!CrawlerJobSender::where('status', 'running')->where('node_id', $node->_id)->exists()) {
                dispatch(new ProcessSendingCrawlerJob($sender))->onConnection('crawler-send');
            }

            return [
                'key' => 'status',
                'message' => 'خزشگر در حال پردازش میباشد'
            ];
        }
    }
}
