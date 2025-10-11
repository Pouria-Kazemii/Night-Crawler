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
    public function go(Crawler $crawler, bool $isUpdate)
    {
        $step = $crawler->crawler_type == 'two_step' ? 1 : 0;

        $crawlerId = (string)$crawler->_id;

        $isFirst = ($crawler->last_run_at ?? null) != null;

        $urls = getUrls($crawler, $isUpdate);

        if ($step === 1 and $isUpdate and !$isFirst) {

            return $this->goSecondStep($crawlerId , true , $isFirst);

        } else {
            $step === 1 ?
                $payloadOptions = getOptions($crawler, $isUpdate, $crawler->two_step['first'], $step) :
                $payloadOptions = getOptions($crawler, $isUpdate);
            return $this->sendRequest($crawler, $urls, $payloadOptions, $crawlerId, $step);
        }
    }

    public function goSecondStep(string $crawlerId, bool $special = false , $jobs_id = null)
    {
        $crawler = Crawler::find($crawlerId);

        $results = CrawlerResult::query();

        if ($special) {

            $jobs = CrawlerJobSender::where('crawler_id', $crawlerId)->where('step', '!=', 1);
            $jobs_id = $jobs->pluck('id')->toArray();
            $results->whereIn('crawler_job_sender_id', $jobs_id);

            $finalResults = $results->get();

            if ($finalResults->isEmpty()) {
                $crawler->schedule == null ?
                    $crawler->update([
                        'status' => 'completed',
                    ]) :
                    $crawler->update([
                        'status' => 'active',
                    ]);
            }

            $urls = $finalResults->pluck('url')->toArray();

        } else {

            $results->whereIn('crawler_job_sender_id', $jobs_id);

            $results->where('content_upgrade', true);

            $finalResults = $results->get();

            if ($crawler->last_used_at == null) {
                foreach ($finalResults as $arr) {
                    $urls[] = $arr->content;
                }
            } else {
                foreach ($finalResults as $arr) {
                    $urls[] = $arr->content_difference;
                }
            }

            if ($finalResults->isEmpty()) {
                $crawler->schedule == null ?
                    $crawler->update([
                        'status' => 'completed',
                    ]) :
                    $crawler->update([
                        'status' => 'active',
                    ]);
            }

        }

        $urls = Arr::flatten($urls);

        $payloadOptions = getOptions($crawler, $crawler->two_step['second']);

        $this->sendRequest($crawler, $urls, $payloadOptions, $crawler->_id, 2);
    }


    public function sendRequest(Crawler $crawler, array $urls, array $payloadOptions, string $crawlerId, int $step)
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
        } else {
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
                    'retries'       => 0,
                    'step'          => $step,
                    'payload'       => $payloadOptions,
                    'processed'     => false,
                    'counts'        => [
                        'url' => count($urls),
                        'success' => 0,
                        'repeated' => 0,
                        'changed' => 0,
                        'not_changed' => 0,
                    ],
                    'started_at'    => now(),
                ]);

                if (!CrawlerJobSender::where('status', 'running')->where('node_id', $node->_id)->exists()) {
                    dispatch(new ProcessSendingCrawlerJob($sender))->onConnection('crawler-send');
                }
            }

            return [
                'key' => 'status',
                'message' => 'خزشگر در حال پردازش میباشد'
            ];
        }
    }
}
