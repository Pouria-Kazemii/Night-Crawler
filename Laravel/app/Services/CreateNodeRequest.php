<?php

namespace App\Services;

use App\Contracts\CreateNodeRequestInterface;
use App\Jobs\ProcessSendingCrawlerJob;
use App\Models\CrawlerNode;
use App\Models\Crawler;
use App\Models\CrawlerJobSender;
use App\Models\CrawlerResult;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class CreateNodeRequest implements CreateNodeRequestInterface
{
    public function go(Crawler $crawler, bool $isUpdate)
    {
        $step = $crawler->crawler_type == 'two_step' ? 1 : 0;

        $crawlerId = (string)$crawler->_id;

        $isFirst = ($crawler->last_run_at ?? null) == null;

        if ($isUpdate and !$isFirst) {

            if (($crawler->schedule['update'] ?? 0) != 0) {
                $crawler->update([
                    'next_update_run_at' => Carbon::now('UTC')->addMinutes((int)$crawler->schedule['update'])
                ]);
            };

            if ($step === 1) {
                $this->goSecondStep($crawlerId, true);
            }
        } else {

            if ((($crawler->schedule['upgrade'] ?? 0) != 0) and !$isUpdate and !$isFirst) {
                $crawler->update([
                    'next_upgrade_run_at' => Carbon::now('UTC')->addMinutes((int)$crawler->schedule['upgrade'])
                ]);
            }

            $urls = getUrls($crawler, $isUpdate);

            $step === 1 ?
                $payloadOptions = getOptions($crawler, $isUpdate, $crawler->two_step['first'], $step) :
                $payloadOptions = getOptions($crawler, $isUpdate);

            return $this->sendRequest($crawler, $urls, $payloadOptions, $crawlerId, $step);
        }
    }

    public function goSecondStep(string $crawlerId, bool $special = false, $jobs_id = null)
    {
        $crawler = Crawler::find($crawlerId);

        $results = CrawlerResult::query();

        if ($special) {

            $jobs = CrawlerJobSender::where('crawler_id', $crawlerId)->where('step', '!=', 1);
            $jobs_id = $jobs->pluck('id')->toArray();
            $results->whereIn('crawler_job_sender_id', $jobs_id);

            $finalResults = $results->get();

            $urls = $finalResults->pluck('url')->toArray();

            $payloadOptions = getOptions($crawler, true, $crawler->two_step['second'], 2);
        } else {

            $results->whereIn('crawler_job_sender_id', $jobs_id);

            $results->where('content_upgrade', true);

            $finalResults = $results->get();

            foreach ($finalResults as $arr) {
                $arr->content_difference == null ?
                    $urls[] = $arr->content :
                    $urls[] = $arr->content_difference;
            }

            $payloadOptions = getOptions($crawler, false, $crawler->two_step['second'], 2);
        }

        if (($urls ?? []) == []) {

            (($crawler->schedule['update'] ?? 0) != 0) or (($crawler->schedule['upgrade'] ?? 0) != 0)
                ?
                $crawler->update([
                    'status' => 'active',
                ])
                :
                $crawler->update([
                    'status' => 'completed',
                ]);
        } else {
            $urls = Arr::flatten($urls);

            $this->sendRequest($crawler, $urls, $payloadOptions, $crawler->_id, 2);
        }
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
                    'crawler_id'       => $crawlerId,
                    'node_id'          => $node->_id,
                    'urls'             => $urlChunk->values()->all(),
                    'status'           => 'queued',
                    'retries'          => 0,
                    'step'             => $step,
                    'payload'          => $payloadOptions,
                    'processed'        => false,
                    'running_priority' => $crawler->crawler_priority,
                    'counts'           => [
                        'url' => count($urlChunk),
                        'success' => 0,
                        'repeated' => 0,
                        'changed' => 0,
                        'not_changed' => 0,
                    ],
                    'started_at'       => now(),
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
