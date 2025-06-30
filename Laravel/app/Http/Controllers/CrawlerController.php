<?php

namespace App\Http\Controllers;

use App\Http\Requests\Crawler\CreateCrawlerRequest;
use App\Http\Requests\Crawler\UpdateCrawlerRequest;
use App\Models\Crawler;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CrawlerController extends Controller
{
    public function index() : View
    {
        $crawlers = Crawler::orderByDesc('created_at')->paginate(15);

        return view('crawler.index' , compact('crawlers'));
    
    }

    public function create() : View 
    {
        return view('crawler.create');
    }

    public function store(CreateCrawlerRequest $request) : RedirectResponse
    {
        $data = $this->normalizeCrawlerInput($request->validated());
        Crawler::create($data);

        return redirect()->route('crawler.index')
        ->with('status','خزشگر با موفقیت ایجاد شد');
    }

    public function edit(Crawler $crawler) : View
    {
        return view('crawler.edit' , compact('crawler'));
    }

    public function update(Crawler $crawler , UpdateCrawlerRequest $request) : RedirectResponse
    {
        $data = $this->normalizeCrawlerInput($request->validated());
        $crawler->update($data);

        return redirect()->route('crawler.index')
        ->with('status','خزشگر با موفقیت بروزرسانی شد');
    }

    public function destroy(Crawler $crawler) : RedirectResponse
    {
        $crawler->delete();

        return redirect()->route('crawler.index')
        ->with('status','خزشگر با موفقیت حذف شد');
    }

    private function normalizeCrawlerInput(array $data): array
    {
        foreach (['selectors', 'pagination_rule', 'auth', 'api_config'] as $field) {
            if (!empty($data[$field]) && is_string($data[$field])) {
                $data[$field] = json_decode($data[$field], true);
            }
        }

        foreach (['start_urls', 'link_filter_rules'] as $field) {
            if (!empty($data[$field]) && is_array($data[$field])) {
                $data[$field] = array_map('trim', explode(',', $data[$field][0]));
            }
        }

        return $data;
    }
}
