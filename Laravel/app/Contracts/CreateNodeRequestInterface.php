<?php

namespace App\Contracts;

use App\Models\Crawler;

interface CreateNodeRequestInterface
{
    public function go(Crawler $crawler , bool $isUpdate);

    public function goSecondStep(string $crawler_id, bool $special ,array $job_ids);

    public function sendRequest(Crawler $crawler, array $urls, array $payloadOptions, string $crawlerId, int $step);
}
