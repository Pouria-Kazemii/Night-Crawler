<?php

namespace App\Contracts;

use App\Models\Crawler;
use Illuminate\Http\Request;

interface CrawlerManagerInterface
{
    public function go(Crawler $crawler,int $retries);

    public function discernment(Request $request);
}
