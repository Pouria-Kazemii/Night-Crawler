<?php

use App\Models\Crawler;

if (!function_exists('getUrls')) {
    function getUrls(Crawler $crawler): array
    {
        $baseUrl = $crawler->base_url;
        if ($crawler->start_urls[0] != '') {

            $fullUrls = array_map(function ($path) use ($baseUrl) {
                return $baseUrl . str_replace('\\', '', $path);
            }, $crawler->start_urls);

            return $fullUrls;
        } elseif ($crawler->url_pattern != null) {

            $start = (int) $crawler->range['start'];
            $end = (int) $crawler->range['end'];

            // Build URLs
            $urls = [];
            for ($i = $start; $i <= $end; $i++) {
                $path = str_replace('{id}', $i, $crawler->url_pattern);
                $urls[] = $baseUrl . $path;
            }

            return $urls;
        } else {
            return [$baseUrl];
        }
    }
}

if (!function_exists('getOptions')) {

    function getOptions(Crawler $crawler , $type = null)
    {
        if($type === null ){
            $type = $crawler->crawler_type;
        }

        switch ($type) {

            case 'static';
                return [
                    'type' => $type,
                    'options' => [
                        'crawl_delay' => $crawler->crawl_delay != null ? $crawler->crawl_delay : 0,
                        'selectors' => $crawler->selectors
                    ]
                ];
                break;

            case 'seed';
                return [
                    'type' => $type,
                    'options' => [
                        'crawl_delay' => $crawler->crawl_delay != null ? $crawler->crawl_delay : 0,
                        'link_filter_rules' => $crawler->link_filter_rules ?? null,
                        'selector' => $crawler->link_selector != null ? $crawler->link_selector : 'null'
                    ]
                ];
                break;

            case 'dynamic';

                return [
                    'type' => $type,
                    'options' => [
                        'crawl_delay' => $crawler->crawl_delay != null ? $crawler->crawl_delay : 0,
                        'selectors' => $crawler->selectors,
                        'max_scrolls' => $crawler->dynamic_limit
                    ]
                ];
                break;

            case 'authenticated';
                return [
                    'type' => $type,
                    'auth' => [
                        'login_url' => $crawler->auth['login_url'],
                        'credentials' => [
                            'username' => $crawler->auth['username'],
                            'password' => $crawler->auth['password'],
                        ],
                        'login_selector' => $crawler->auth['username_selector'],
                        'password_selector' => $crawler->auth['password_selector']
                    ],
                    'options' => [
                        'crawl_delay' => $crawler->crawl_delay != null ? $crawler->crawl_delay : 0,
                        'selectors' => $crawler->selectors
                    ]
                ];
                break;

            case 'api'; //TODO
                return [
                    'type' => $type,
                    'options' => [
                        'crawl_delay' => $crawler->crawl_delay != null ? $crawler->crawl_delay : 0,
                    ]
                ];
                break;

            case 'paginated';
                return [
                    'type' => $type,
                    'next_page_selector' => $crawler->pagination_rule['next_page_selector'],
                    'options' => [
                        'crawl_delay' => $crawler->crawl_delay != null ? $crawler->crawl_delay : 0,
                        'limit' => $crawler->pagination_rule['limit'] ?? 1,
                        'selectors' => $crawler->selectors
                    ]
                ];
                break;
        }
    }
}
