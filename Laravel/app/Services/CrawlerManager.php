<?php

namespace App\Services;

use App\Models\CrawlerJob;
use App\Models\CrawlerNode;
use App\Contracts\CrawlerManagerInterface;
use App\Models\Crawler;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\CrawlerResult;
use Illuminate\Support\Facades\Cache;

class CrawlerManager implements CrawlerManagerInterface
{
    private Crawler $crawler;

    public function __construct(Crawler $crawler)
    {
        return $this->crawler = $crawler;
    }

    public function go(int $retries = 0)
    {
        $urls = $this->getUrls();
        $crawlerId = (string)$this->crawler->_id;
        $payloadOptions = $this->getOptions();

        // 1. Get available nodes sorted by latency (lowest first)
        $nodes = CrawlerNode::where('status', 'active')
            ->whereNotNull('latency')
            ->orderBy('latency', 'asc')
            ->get();

        if ($nodes->isEmpty()) {
            throw new \Exception('No active crawler nodes available.');
        }

        // 2. Split URLs into small chunks (e.g., 10 per chunk)
        $chunks = collect($urls)->chunk(ceil(count($urls) / $nodes->count()))->values();

        foreach ($chunks as $index => $urlChunk) {
            // 3. Assign node in round-robin fashion
            $node = $nodes[$index % $nodes->count()];

            // 4. Create crawler_jobs record
            $job = CrawlerJob::create([
                'crawler_id'    => $crawlerId,
                'node_id'       => $node->_id,
                'urls'          => $urlChunk->values()->all(),
                'status'        => 'running',
                'started_at'    => now(),
                'retries'       => $retries,
                'last_used_at'  => now()
            ]);

            // 5. Prepare and send job payload to Python node
            $payloadOptions['urls'] = $urlChunk->values()->all();
            $payloadOptions['meta'] = [
                'job_id' => $job->_id,
                'crawler_id' => $crawlerId
            ];

            $payloadJson = json_encode($payloadOptions);
            $token = env('CRAWLER_API_TOKEN');
            $cmd = "curl -X POST http://{$node->ip_address}:{$node->port}/crawl " .
                "-H 'Content-Type: application/json' " .
                "-H 'Authorization: Bearer {$token}' " .
                "-d '{$payloadJson}' > /dev/null 2>&1 &";
            exec($cmd);
        }
    }

    public function discernment(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(['dynamic', 'seed', 'static', 'paginated', 'api', 'authernticated'])],
            'original_url' => 'required',
            'final_url' => 'nullable',
            'content' => 'nullable',
            'error' => 'nullable',
            'is_last' => 'required|boolean',
            'status_code' => 'required|integer',
            'meta' => 'required|array'
        ]);

        $jobId = $validated['meta']['job_id'];
        $crawlerId = $validated['meta']['crawler_id'];

        // ✅ Handle successful results
        if ($validated['status_code'] === 200) {
            $content = $validated['type'] === 'seed'
                ? array_map('urldecode', $validated['content'] ?? [])
                : $validated['content'];

            CrawlerResult::updateOrCreate(
                [
                    'final_url' => urldecode($validated['final_url']),
                    'url' => $validated['original_url'],
                ],
                [
                    'job_id' => $jobId,
                    'content' => $content,
                ]
            );
        }
        // ❌ Failed result, store in cache
        else {
            $failedKey = $jobId . '_failed_urls';
            $statusKey = $jobId . '_status';

            $failedUrls = Cache::get($failedKey, []);
            $failedUrls[] = [
                $validated['original_url'] => [
                    'error' => $validated['error'],
                    'status_code' => $validated['status_code']
                ]
            ];
            Cache::forever($failedKey, $failedUrls);
            Cache::forever($statusKey, 'failed');
        }

        // ✅ If this was the last response for this job
        if ($validated['is_last']) {
            $crawler = Crawler::where('_id', $crawlerId)
                ->with(['crawlerJobs' => fn($q) => $q->where('status', 'running')])
                ->first();

            $statusKey = $jobId . '_status';
            $failedKey = $jobId . '_failed_urls';
            $jobStatus = Cache::get($statusKey) ?? 'success';

            $update = ['status' => $jobStatus];
            if ($jobStatus === 'failed') {
                $update['failed_url'] = Cache::get($failedKey, []);
            }

            CrawlerJob::where('_id', $jobId)->update($update);

            // ✅ If this was the last running job
            if ($crawler && count($crawler->crawlerJobs) === 1) {
                $allJobs = Crawler::find($crawlerId)?->crawlerJobs ?? [];
                $allSuccess = true;

                foreach ($allJobs as $job) {
                    if (Cache::get($job->_id . '_status') === 'failed') {
                        $allSuccess = false;
                    }
                    Cache::forget($job->_id . '_status');
                    Cache::forget($job->_id . '_failed_urls');
                }

                $crawlerStatus = match (true) {
                    $allSuccess && !$crawler->schedule['frequency'] => 'completed',
                    $allSuccess => 'active',
                    default => 'error',
                };

                Crawler::where('_id', $crawlerId)->update(['crawler_status' => $crawlerStatus]);
            }
        }
    }

    private function getUrls(): array
    {
        $baseUrl = $this->crawler->base_url;
        if ($this->crawler->start_urls[0] != '') {

            $fullUrls = array_map(function ($path) use ($baseUrl) {
                return $baseUrl . str_replace('\\', '', $path);
            }, $this->crawler->start_urls);

            return $fullUrls;
        } elseif ($this->crawler->url_pattern != null) {

            $start = (int) $this->crawler->range['start'];
            $end = (int) $this->crawler->range['end'];

            // Build URLs
            $urls = [];
            for ($i = $start; $i <= $end; $i++) {
                $path = str_replace('{id}', $i, $this->crawler->url_pattern);
                $urls[] = $baseUrl . $path;
            }

            return $urls;
        } else {
            return [$baseUrl];
        }
    }


    private function getOptions()
    {
        switch ($this->crawler->crawler_type) {

            case 'static';
                return [
                    'type' => $this->crawler->crawler_type,
                    'options' => [
                        'crawl_delay' => $this->crawler->crawl_delay != null ? $this->crawler->crawl_delay : 0,
                        'selectors' => $this->crawler->selectors
                    ]
                ];
                break;

            case 'seed';
                return [
                    'type' => $this->crawler->crawler_type,
                    'options' => [
                        'crawl_delay' => $this->crawler->crawl_delay != null ? $this->crawler->crawl_delay : 0,
                        'max_depth' => $this->crawler->max_depth != null ? $this->crawler->max_depth : 0,
                        'link_filter_rules' => $this->crawler->link_filter_rules ?? null,
                        'selector' => $this->crawler->selectors[0]['selector'] != 'all' ? $this->crawler->selectors[0]['selector'] : 'null' 
                    ]
                ];
                break;

            case 'dynamic';

                return [
                    'type' => $this->crawler->crawler_type,
                    'options' => [
                        'crawl_delay' => $this->crawler->crawl_delay != null ? $this->crawler->crawl_delay : 0,
                        'selectors' => $this->crawler->selectors
                    ]
                ];
                break;

            case 'authenticated';
                return [
                    'type' => $this->crawler->crawler_type,
                    'auth' => [
                        'login_url' => $this->crawler->auth['login_url'],
                        'credentials' => [
                            'username' => $this->crawler->auth['username'],
                            'password' => $this->crawler->auth['password'],
                        ]
                    ],
                    'options' => [
                        'crawl_delay' => $this->crawler->crawl_delay != null ? $this->crawler->crawl_delay : 0,
                        'selectors' => $this->crawler->selectors
                    ]
                ];
                break;

            case 'api'; //TODO
                return [
                    'type' => $this->crawler->crawler_type,
                    'options' => [
                        'crawl_delay' => $this->crawler->crawl_delay != null ? $this->crawler->crawl_delay : 0,
                    ]
                ];
                break;

            case 'paginated';
                return [
                    'type' => $this->crawler->crawler_type,
                    'next_selector' => $this->crawler->pagination_rule['next_page_selector'],
                    'options' => [
                        'crawl_delay' => $this->crawler->crawl_delay != null ? $this->crawler->crawl_delay : 0,
                        'limit' => $this->crawler->pagination_rule['limit'] ?? 1,
                        'selectors' => $this->crawler->selectors
                    ]
                ];
                break;
        }
    }
}
