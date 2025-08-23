<?php

namespace App\Contracts;

use App\Models\Crawler;

interface CreateNodeRequestInterface
{
    public function go(Crawler $crawler,int $retries);

    public function goSecondStep(string $crawler_id, int $retries, array $job_ids);

    public function sendRequest(Crawler $crawler, array $urls, array $payloadOptions, string $crawlerId, int $step, int $retries = 0);
}
