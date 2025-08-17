<?php

namespace App\Console\Commands;

use App\Models\CrawlerNode;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

class AllCommands extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:all-commands';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This is a command for run all other commands';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        foreach (CrawlerNode::where('status' , 'down') as $node) {
            try {
                $start = microtime(true); // Start measuring time
                $response = Http::timeout(2)->get("http://{$node->ip_address}:{$node->port}/health");

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
                    ]);
                }
            } catch (\Exception $e) {
                $node->update([
                    'status' => 'down',
                    'latency' => null,
                ]);
            }
        }

        Artisan::call('app:check-failed-job');

        Artisan::call('app:check-running-job');

        Artisan::call('app:check-crawler-schedule');
    }
}
