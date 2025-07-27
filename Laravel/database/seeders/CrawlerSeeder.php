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
                'title' => 'سی بوک',
                'description' => 'نویسندگان آیدی 178840 تا ایدی 178890',
                'crawler_status' => 'active',
                'crawler_type' => 'static',
                'base_url' => 'https://www.30book.com',
                'url_pattern' => '/book/{id}',
                'range' => [
                    'start' => 178840,
                    'end' => 178890
                ],
                'pagination_rule' => [
                    'next_page_selector' => null,
                    'limit' => null,
                ],
                'auth' => [
                    'login_url' => null,
                    'username' => null,
                    'password' => null
                ],
                'api_config' => [
                    'method' => null,
                    'token' => null
                ],
                'selectors' => [
                    [
                        'key' => 'creators',
                        'selector' => 'div.row.gx-2.align-items-center.mt-4 a.product-main-link',
                        'full_html' => false
                    ]

                ],
                'schedule' => [
                    'frequency' => 'daily',
                    'time' => '02:00'
                ],
                'start_urls' => [""],
                'crawl_delay' => '1',
            ],

            // Dynamic
            [
                'title' => 'دیجی کالا',
                'description' => 'محصولات با تگ gaming set',
                'crawler_status' => 'active',
                'crawler_type' => 'dynamic',
                'base_url' => 'https://www.digikala.com',
                'url_pattern' => null,
                'range' => [
                    'start' => null,
                    'end' => null
                ],
                'pagination_rule' => [
                    'next_page_selector' => null,
                    'limit' => null,
                ],
                'auth' => [
                    'login_url' => null,
                    'username' => null,
                    'password' => null
                ],
                'api_config' => [
                    'method' => null,
                    'token' => null
                ],
                'selectors' => [
                    [
                        'key' => 'title',
                        'selector' => 'h3.ellipsis-2.text-body2-strong.text-neutral-700.styles_VerticalProductCard__productTitle__6zjjN',
                        'full_html' => false
                    ]

                ],
                'schedule' => [
                    'frequency' => 'daily',
                    'time' => '02:00'
                ],
                'start_urls' => ["/tags/gaming-set/"],
                'crawl_delay' => '1',
            ],

            // Paginated
            [
                'title' => 'فروشگاه کفش',
                'description' => 'سه صفحه اول فروشگاه',
                'crawler_status' => 'static',
                'crawler_type' => 'paginated',
                'base_url' => 'https://amiransport.com',
                'url_pattern' => null,
                'range' => [
                    'start' => null,
                    'end' => null
                ],
                'pagination_rule' => [
                    'next_page_selector' => "ul.page-numbers a.next.page-numbers",
                    'limit' => "3",
                ],
                'auth' => [
                    'login_url' => null,
                    'username' => null,
                    'password' => null
                ],
                'api_config' => [
                    'method' => null,
                    'token' => null
                ],
                'selectors' => [
                    [
                        'key' => 'price',
                        'selector' => 'div.product-element-bottom div.wrap-price',
                        'full_html' => true
                    ],
                    [
                        'key' => 'title',
                        'selector' => 'div.product-element-bottom h3.wd-entities-title',
                        'full_html' => false
                    ]

                ],
                'schedule' => [
                    'frequency' => null,
                    'time' => null
                ],
                'start_urls' => ["/shop/page/1/"],
                'crawl_delay' => '0',
            ],


            // Authenticated
            [
                'title' => 'گیت هاب',
                'description' => 'گرفتن بیو صفحه گیت هاب',
                'crawler_status' => 'active',
                'crawler_type' => 'authenticated',
                'base_url' => 'https://github.com',
                'url_pattern' => null,
                'range' => [
                    'start' => null,
                    'end' => null
                ],
                'pagination_rule' => [
                    'next_page_selector' => null,
                    'limit' => null,
                ],
                'auth' => [
                    'login_url' => 'https://github.com/login',
                    'username' => 'user_name',
                    'password' => 'password'
                ],
                'api_config' => [
                    'method' => null,
                    'token' => null
                ],
                'selectors' => [
                    [
                        'key' => 'bio',
                        'selector' => 'form-control form-control user-profile-bio-field js-length-limited-input',
                        'full_html' => false
                    ]

                ],
                'schedule' => [
                    'frequency' => 'daily',
                    'time' => '02:00'
                ],
                'start_urls' => ["/settings/profile"],
                'crawl_delay' => '1',
            ],

            //TODO
            // API
            // [
            //     'title' => 'نویسندگان محصولات سی بوک',
            //     'description' => 'ایدی 178840 تا ایدی 178890',
            //     'crawler_status' => 'active',
            //     'crawler_type' => 'static',
            //     'base_url' => 'https://www.30book.com/book/178840',
            //     'url_pattern' => null,
            //     'range' => [
            //         'start' => null,
            //         'end' => null
            //     ],
            //     'pagination_rule' => [
            //         'next_page_selector' => null,
            //         'limit' => null,
            //     ],
            //     'auth' => [
            //         'login_url' => null,
            //         'username' => null,
            //         'password' => null
            //     ],
            //     'api_config' => [
            //         'method' => null,
            //         'token' => null
            //     ],
            //     'selectors' => [
            //         [
            //             'key' => 'creators',
            //             'selector' => 'div.row.gx-2.align-items-center.mt-4 a.product-main-link',
            //             'full_html' => false
            //         ]

            //     ],
            //     'schedule' => [
            //         'frequency' => 'daily',
            //         'time' => '02:00'
            //     ],
            //     'start_urls' => [""],
            //     'max_depth' => '0',
            //     'link_filter_rules' => [""],
            //     'crawl_delay' => '1',
            // ],

            // Seed
            [
                'title' => 'تابناک',
                'description' => 'کرول جدیدترن اخبار اقتصادی و دنیا	',
                'crawler_status' => 'active',
                'crawler_type' => 'seed',
                'base_url' => 'https://www.tabnak.ir',
                'url_pattern' => null,
                'range' => [
                    'start' => null,
                    'end' => null
                ],
                'pagination_rule' => [
                    'next_page_selector' => null,
                    'limit' => null,
                ],
                'auth' => [
                    'login_url' => null,
                    'username' => null,
                    'password' => null
                ],
                'api_config' => [
                    'method' => null,
                    'token' => null
                ],
                'selectors' => [
                    [
                        'key' => 'search',
                        'selector' => 'div#latestNews',
                        'full_html' => false
                    ]

                ],
                'schedule' => [
                    'frequency' => 'daily',
                    'time' => '02:00'
                ],
                'start_urls' => ["/fa/world","/fa/economic"],
                'max_depth' => '0',
                'link_filter_rules' => ["/news"],
                'crawl_delay' => '1',
            ],
        ];

        foreach ($crawlers as $crawler) {
            Crawler::create($crawler);
        }
    }
}
