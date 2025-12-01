<?php

use App\Models\Crawler;

if (!function_exists('getUrls')) {
    function getUrls(Crawler $crawler, bool $isUpdate): array
    {
        $baseUrl = $crawler->base_url;

        if ($crawler->start_urls[0] != '') {

            $fullUrls = array_map(function ($path) use ($baseUrl) {
                return $baseUrl . str_replace('\\', '', $path);
            }, $crawler->start_urls);

            return $fullUrls;
        } elseif ($crawler->url_pattern != null) {

            if ($isUpdate) {
                $start = (int) $crawler->range['start'];
                $end = (int) $crawler->range['end'];
            } else {
                $start = (int) $crawler->upgrade_range['start'];
                $end = (int) $crawler->upgrade_range['end'];
            }

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

    function getOptions(Crawler $crawler, bool $isUpdate, $type = null, $step = 0)
    {
        if ($type === null) {
            $type = $crawler->crawler_type;
        }

        if ($step != 2) {
            $crawlerDelay = $crawler->crawl_delay ?? 0;
        } else {
            $crawlerDelay = $crawler->crawl_delay_second_step ?? 0;
        }

        switch ($type) {

            case 'static';
                return [
                    'type' => $type,
                    'options' => [
                        'separate_items' => $crawler->array_selector != null ? $crawler->array_selector : false,
                        'crawl_delay' => $crawlerDelay,
                        'selectors' => $isUpdate ? $crawler->update_selectors : $crawler->selectors
                    ]
                ];
                break;

            case 'seed';
                return [
                    'type' => $type,
                    'options' => [
                        'crawl_delay' => $crawlerDelay,
                        'link_filter_rules' => $crawler->link_filter_rules ?? null,
                        'selector' => $crawler->link_selector != null ? $crawler->link_selector : 'null'
                    ]
                ];
                break;

            case 'dynamic';

                if ($step == 1) {
                    $selectorKey = 'selector';
                    $selectorValue = $crawler->link_selector;
                } else {
                    $selectorKey = 'selectors';
                    $selectorValue = $isUpdate ? $crawler->update_selectors : $crawler->selectors;
                }

                return [
                    'type' => $type,
                    'options' => [
                        'separate_items' => $crawler->array_selector != null ? $crawler->array_selector : false,
                        'crawl_delay' => $crawlerDelay,
                        $selectorKey => $selectorValue,
                        'max_scrolls' => $isUpdate ? $crawler->dynamic_limit : $crawler->upgrade_dynamic_limit
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
                        'separate_items' => $crawler->array_selector != null ? $crawler->array_selector : false,
                        'crawl_delay' => $crawlerDelay,
                        'selectors' => $isUpdate ? $crawler->update_selectors : $crawler->selectors
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
                        'separate_items' => $crawler->array_selector != null ? $crawler->array_selector : false,
                        'crawl_delay' => $crawlerDelay,
                        'limit' => $crawler->pagination_rule['limit'] ?? 1,
                        'selectors' => $isUpdate ? $crawler->update_selectors : $crawler->selectors
                    ]
                ];
                break;
        }
    }
}
