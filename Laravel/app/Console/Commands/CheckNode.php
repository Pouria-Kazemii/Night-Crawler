<?php

namespace App\Console\Commands;

use App\Models\CrawlerNode;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CheckNode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-nodes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This is a command check accessibility nodes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $nodes = CrawlerNode::where('last_used_at', '<=', Carbon::now('UTC')->addMinutes(-5))->get();
        foreach ($nodes as $node) {
            try {
                $start = microtime(true); // Start measuring time

                $response = Http::timeout(2)->get("{$node->protocol}://{$node->ip_address}:{$node->port}/health");

                $latency = round((microtime(true) - $start) * 1000); // in ms

                if ($response->successful() && $response['status'] === 'ok') {
                    $node->update([
                        'status' => 'active',
                        'last_used_at' => now(),
                        'latency' => $latency,
                    ]);
                } else {
                    $node->update([
                        'status' => 'down',
                        'latency' => null,
                        'last_used_at' => now(),
                    ]);
                }
            } catch (\Exception $e) {
                $node->update([
                    'status' => 'down',
                    'latency' => null,
                    'last_used_at' => now(),
                ]);
            }
        }
    }
}
