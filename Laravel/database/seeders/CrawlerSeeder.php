<?php

namespace Database\Seeders;

use App\Models\Crawler;
use Illuminate\Database\Seeder;

class CrawlerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $examples = [
            'static' => [
                'selectors' => [
                    'title' => 'h1.title',
                    'content' => '.content > p',
                ]
            ],
            'dynamic' => [
                'selectors' => [
                    'title' => 'h2.dynamic-title',
                    'description' => '.dynamic-description',
                ]
            ],
            'paginated' => [
                'selectors' => [
                    'item' => '.product',
                    'price' => '.price'
                ],
                'pagination_rule' => [
                    'next_page_selector' => '.pagination .next',
                    'max_pages' => 10,
                ]
            ],
            'authenticated' => [
                'selectors' => [
                    'user' => '.user-name',
                    'email' => '.user-email'
                ],
                'auth' => [
                    'login_url' => 'https://example.com/login',
                    'username_field' => 'email',
                    'password_field' => 'password',
                    'credentials' => [
                        'username' => 'test@example.com',
                        'password' => 'secret123',
                    ],
                ]
            ],
            'api' => [
                'api_config' => [
                    'endpoint' => 'https://api.example.com/items',
                    'method' => 'GET',
                    'headers' => [
                        'Authorization' => 'Bearer TOKEN123',
                        'Accept' => 'application/json',
                    ]
                ]
            ],
            'seed' => [] // No selectors, no pagination, no auth, no api_config
        ];

        foreach ($examples as $type => $specificData) {
            Crawler::create(array_merge([
                'title' => "نمونه خزنده $type",
                'description' => "این یک نمونه خزنده از نوع $type است.",
                'crawler_status' => 'active',
                'crawler_type' => $type,
                'base_url' => 'https://example.com',
                'start_urls' => ['https://example.com/start'],
                'schedule' => [
                    'frequency' => 'daily',
                    'time' => '02:00'
                ],
                'max_depth' => 3,
                'link_filter_rules' => ['*.pdf', '*logout*'],
                'crawl_delay' => 1
            ], $specificData));
        }
    }
}