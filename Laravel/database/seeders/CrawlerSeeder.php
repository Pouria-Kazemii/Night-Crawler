<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Crawler;

class CrawlerSeeder extends Seeder
{
    public function run(): void
    {
        Crawler::insert([
            [
                'title' => 'تابناک',
                'description' => 'کرول جدیدترن اخبار اقتصادی ورزشی و بین المللی	',
                'crawler_status' => 'active',
                'crawler_type' => 'two_step',
                'base_url' => 'https://www.tabnak.ir',
                'url_pattern' => null,
                'pagination_rule' => null,
                'auth' => null,
                'api_config' => null,
                'schedule' => 60,
                'selectors' => '[
                {"key":"title","selector":"div.top_news_title","full_html":false},
                {"key":"body","selector":"div#newsMainBody.body","full_html":true}
                ]',
                'two_step' => '{"first":"seed","second":"static"}',
                'start_urls' => '["\\/fa\\/economic","\\/fa\\/world","\\/fa\\/sport"]',
                'link_selector' => 'div#latestNews',
                'link_filter_rules' => '["\\/news"]',
                'crawl_delay' => '1',
                'range' => '{"start":null,"end":null}',
            ],
            [
                'title' => 'سی بوک',
                'description' => 'نویسندگان آیدی 178840 تا ایدی 178845',
                'crawler_status' => 'active',
                'crawler_type' => 'static',
                'base_url' => 'https://www.30book.com',
                'url_pattern' => '/book/{id}',
                'start_urls' => '[""]',
                'pagination_rule' => null,
                'auth' => null,
                'api_config' => null,
                'selectors' => '[
                {"key":"creators","selector":"div.col-sm.mt-3.mt-sm-0 div.row.gx-2 a.product-main-link","full_html":false},
                {"key":"publisher","selector":"div.col-sm.mt-3.mt-sm-0 div.row.gx-2.align-items-center.mt-4 a.product-main-link","full_html":false},
                {"key":"no-discount-price","selector":"div.row.gx-2.mt-4 div.product-price","full_html":false},
                {"key":"discount-price","selector":"div.row.gx-2.mt-4 div.product-final-price","full_html":false}
                ]',
                'schedule' => 10,
                'crawl_delay' => null,
                'range' => '{"start":"178840","end":"178845"}',
                'link_selector' => null,
                'two_step' => '{"first":null,"second":null}'
            ]
        ]);
    }
}
