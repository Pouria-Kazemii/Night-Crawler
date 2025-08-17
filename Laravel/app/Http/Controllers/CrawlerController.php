<?php

namespace App\Http\Controllers;

use App\Constants\CrawlerTypes;
use App\Http\Requests\Crawler\CreateCrawlerRequest;
use App\Http\Requests\Crawler\UpdateCrawlerRequest;
use App\Models\Crawler;
use App\Services\CrawlerManager;
use App\Services\CreateNodeRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CrawlerController extends Controller
{
    public function index(): View
    {
        $crawlers = Crawler::orderByDesc('created_at')->paginate(15);

        return view('crawler.index', compact('crawlers'));
    }

    public function create(): View
    {
        $crawlerTypes = CrawlerTypes::all();

        return view('crawler.create' , compact('crawlerTypes'));
    }

    public function store(CreateCrawlerRequest $request): RedirectResponse
    {
        $data = $this->normalizeCrawlerInput($request->validated());
        Crawler::create($data);

        return redirect()->route('crawler.index')
            ->with('status', 'خزشگر با موفقیت ایجاد شد');
    }

    public function edit(Crawler $crawler): View
    {
        $crawlerTypes = CrawlerTypes::all();
        
        return view('crawler.edit', compact('crawler','crawlerTypes'));
    }

    public function update(Crawler $crawler, UpdateCrawlerRequest $request): RedirectResponse
    {
        $data = $this->normalizeCrawlerInput($request->validated());
        $crawler->update($data);

        return redirect()->route('crawler.index')
            ->with('status', 'خزشگر با موفقیت بروزرسانی شد');
    }

    public function destroy(Crawler $crawler): RedirectResponse
    {
        $crawler->delete();

        return redirect()->route('crawler.index')
            ->with('status', 'خزشگر با موفقیت حذف شد');
    }

    public function go(Crawler $crawler): RedirectResponse
    {

        $crawlerManager = app(CreateNodeRequest::class);

        $crawlerManager->go($crawler);

        return redirect()->route('crawler.index')
            ->with('status', 'خزش با موفقیت شروع شد ');
    }

    public function showResults(Crawler $crawler)
    {
        

    }


    private function normalizeCrawlerInput(array $data): array
    {
        foreach (['pagination_rule', 'auth', 'api_config' , 'two_step'] as $field) {
            if (!empty($data[$field]) && is_string($data[$field])) {
                $data[$field] = json_decode($data[$field], true);
            }
        }

        foreach (['start_urls', 'link_filter_rules'] as $field) {
            if (!empty($data[$field]) && is_array($data[$field])) {
                $data[$field] = array_map('trim', explode(',', $data[$field][0]));
            }
        }

        if (!empty($data['selectors']) and $data['selectors'] != null) {
            if (is_string($data['selectors'])) {
                // Handle old comma-separated format
                $selectors = array_map('trim', explode(',', $data['selectors']));
                $data['selectors'] = array_map(function ($selector) {
                    return [
                        'key' => '',
                        'selector' => $selector,
                        'full_html' => false, // Default value for old format
                    ];
                }, $selectors);
            } elseif (is_array($data['selectors'])) {
                // Filter out empty selector entries
                $data['selectors'] = array_filter($data['selectors'], function ($item) {
                    return !empty($item['selector']);
                });

                // Normalize full_html field and reindex array
                $data['selectors'] = array_values(array_map(function ($item) {
                    return [
                        'key' => $item['key'] ?? '',
                        'selector' => $item['selector'],
                        'full_html' => !empty($item['full_html']), // Convert to true/false
                    ];
                }, $data['selectors']));
            }
        } else {
            $data['selectors'] = [];
        }



        return $data;
    }

}
