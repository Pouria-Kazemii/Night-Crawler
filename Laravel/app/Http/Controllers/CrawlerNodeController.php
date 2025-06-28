<?php

namespace App\Http\Controllers;

use App\Http\Requests\CrawlerNode\StoreCrawlerNodeRequest;
use App\Http\Requests\CrawlerNode\UpdateCrawlerNodeRequest;
use App\Models\CrawlerNode;
use Illuminate\Http\RedirectResponse;
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
}
