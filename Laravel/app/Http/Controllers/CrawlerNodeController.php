<?php

namespace App\Http\Controllers;

use App\Http\Requests\CrawlerNode\StoreCrawlerNodeRequest;
use App\Http\Requests\CrawlerNode\UpdateCrawlerNodeRequest;
use App\Models\Crawler;
use App\Models\CrawlerNode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class CrawlerNodeController extends Controller
{
    public function index() : View
    {
        $crawlNodes = CrawlerNode::orderByDesc('created_at')->paginate(15);

        return view('crawlerNode.index' , compact('crawlNodes'));
    }

    public function create() : View
    {
        return view('crawlerNode.create');
    }

    public function store(StoreCrawlerNodeRequest $request): RedirectResponse
    {
        CrawlerNode::create($request->validated());

        return redirect()->route('crawl-nodes.index')
        ->with('status', 'پروکسی با موفقیت ایجاد شد.');
    }


    public function edit(CrawlerNode $crawlerNode): View
    {
        return view('crawlerNode.edit', compact('crawlerNode'));
    }

    public function update(UpdateCrawlerNodeRequest $request, CrawlerNode $crawlerNode): RedirectResponse
    {
        $crawlerNode->update($request->validated());

        return redirect()->route('crawl-nodes.index')
            ->with('status', 'پروکسی با موفقیت به‌روزرسانی شد.');
    }
    
    public function destroy(CrawlerNode $crawlerNode)
    {
        $crawlerNode->delete();

        return redirect()->route('crawl-nodes.index')
            ->with('status', 'پروکسی با موفقیت حذف شد.');
    }

    public function pingNode(CrawlerNode $crawlerNode) : RedirectResponse
    {
        $this->checkNodeHealth($crawlerNode);

        return back()->with('status', 'وضعیت پروکسی به‌روزرسانی شد.');
    }

    private function checkNodeHealth(CrawlerNode $node): void
    {
        try {
            $start = microtime(true); // Start measuring time
            $response = Http::timeout(2)->get("http://{$node->ip_address}:{$node->port}/health");

            $latency = round((microtime(true) - $start) * 1000); // in ms
            dd($response);

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
}
