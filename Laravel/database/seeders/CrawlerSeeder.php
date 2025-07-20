<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Crawler;

class CrawlerSeeder extends Seeder
{
    public function run(): void
    {
        $crawlers = [
            // Static
            [
                'title' => 'نمونه خزنده static',
                'description' => 'این یک نمونه خزنده از نوع static است.',
                'crawler_status' => 'active',
                'crawler_type' => 'static',
                'base_url' => 'https://developer.mozilla.org',
                'start_urls' => ["https://developer.mozilla.org/en-US/docs/Web/HTML"],
                'selectors' => [
                    'title' => 'h1.title',
                    'content' => '.content > p'
                ],
                'schedule' => [
                    'frequency' => 'daily',
                    'time' => '02:00'
                ],
                'max_depth' => 3,
                'link_filter_rules' => ['*.pdf', '*logout*'],
                'crawl_delay' => 1,
            ],

            // Dynamic
            [
                'title' => 'نمونه خزنده dynamic',
                'description' => 'دریافت محتوا با رندر JS از دیجی‌کالا.',
                'crawler_status' => 'active',
                'crawler_type' => 'dynamic',
                'base_url' => 'https://www.digikala.com',
                'start_urls' => ['https://www.digikala.com/search/?q=iphone'],
                'selectors' => [
                    'title' => 'h1.c-product__title',
                    'price' => '.c-price__value'
                ],
                'schedule' => [
                    'frequency' => 'daily',
                    'time' => '03:00'
                ],
                'max_depth' => 1,
                'link_filter_rules' => [],
                'crawl_delay' => 1,
            ],

            // Paginated
            [
                'title' => 'نمونه خزنده paginated',
                'description' => 'خزنده صفحات بلاگ وردپرس',
                'crawler_status' => 'active',
                'crawler_type' => 'paginated',
                'base_url' => 'https://wordpress.com/blog',
                'start_urls' => ['https://wordpress.com/blog'],
                'selectors' => [
                    'title' => 'h2.entry-title',
                    'content' => '.entry-content p'
                ],
                'pagination_rule' => [
                    'selector' => '.pagination-next a',
                    'limit' => 5
                ],
                'schedule' => [
                    'frequency' => 'daily',
                    'time' => '04:00'
                ],
                'max_depth' => 2,
                'link_filter_rules' => [],
                'crawl_delay' => 1,
            ],

            // Authenticated
            [
                'title' => 'نمونه خزنده authenticated',
                'description' => 'ورود به داشبورد و دریافت اطلاعات پروفایل',
                'crawler_status' => 'active',
                'crawler_type' => 'authenticated',
                'base_url' => 'https://github.com',
                'start_urls' => ['https://github.com/settings/profile'],
                'selectors' => [
                    'username' => '#user_profile_name',
                    'bio' => '#user_profile_bio'
                ],
                'auth' => [
                    'login_url' => 'https://github.com/login',
                    'username_selector' => '#login_field',
                    'password_selector' => '#password',
                    'submit_selector' => "input[type='submit']",
                    'credentials' => [
                        'username' => 'myuser',
                        'password' => 'mypassword'
                    ]
                ],
                'schedule' => [
                    'frequency' => 'daily',
                    'time' => '05:00'
                ],
                'max_depth' => 1,
                'crawl_delay' => 1,
            ],

            // API
            [
                'title' => 'نمونه خزنده API',
                'description' => 'دریافت اخبار نیویورک تایمز با API',
                'crawler_status' => 'active',
                'crawler_type' => 'api',
                'base_url' => 'https://api.nytimes.com',
                'start_urls' => ['https://api.nytimes.com/svc/topstories/v2/home.json?api-key=DEMO_KEY'],
                'api_config' => [
                    'headers' => [
                        'Accept' => 'application/json'
                    ],
                    'extract_path' => 'results[*].title'
                ],
                'schedule' => [
                    'frequency' => 'hourly',
                    'time' => '00:30'
                ],
            ],

            // Seed
            [
                'title' => 'جمع‌آوری لینک‌های دانشگاه شریف',
                'description' => 'لینک‌های عمومی سایت sharif.edu برای خوراک',
                'crawler_status' => 'active',
                'crawler_type' => 'seed',
                'base_url' => 'https://sharif.edu',
                'start_urls' => ['https://sharif.edu'],
                'link_filter_rules' => ['*mailto*', '*#*', '*login*'],
                'schedule' => [
                    'frequency' => 'weekly',
                    'time' => '01:00'
                ],
                'max_depth' => 1,
                'crawl_delay' => 1,
            ],
        ];

        foreach ($crawlers as $crawler) {
            Crawler::create($crawler);
        }
    }
}
