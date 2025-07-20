<?php

namespace App\Contracts;

use Illuminate\Http\Request;

interface CrawlerManagerInterface
{
    public function go(int $retries);

    public function discernment(Request $request);
}
