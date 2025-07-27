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
use Illuminate\Support\Facades\Log;

class CrawlerManager implements CrawlerManagerInterface
{

    public function go(Crawler $crawler, int $retries = 0)
    {
        $urls = getUrls($crawler);
        $crawlerId = (string)$crawler->_id;
        $payloadOptions = getOptions($crawler);

        // 1. Get available nodes sorted by latency (lowest first)
        $nodes = CrawlerNode::where('status', 'active')
            ->whereNotNull('latency')
            ->orderBy('latency', 'asc')
            ->get();

        if ($nodes->isEmpty()) {
            throw new \Exception('No active crawler nodes available.');
        }

        $crawler->update(['status' => 'active', 'last_run_at' => now()]);

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
            'type' => ['required', Rule::in(['dynamic', 'seed', 'static', 'paginated', 'api', 'authenticated'])],
            'original_url' => 'required',
            'final_url' => 'nullable',
            'content' => 'nullable',
            'error' => 'nullable',
            'is_last' => 'required|boolean',
            'status_code' => 'required|integer',
            'meta' => 'required|array',
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
                    'encrypt_url' => hash('sha256' , $validated['final_url'])
                ],
                [
                    'final_url' => urldecode($validated['final_url']),
                    'url' => $validated['original_url'],
                    'job_id' => $jobId,
                    'content' => $content,
                    'stats' => isset($validated['stats']) ? $validated['content'] : null
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
}
