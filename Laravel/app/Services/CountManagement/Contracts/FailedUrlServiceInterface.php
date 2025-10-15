<?php
namespace App\Services\CountManagement\Contracts;

use App\Services\CountManagement\DTOs\FailedUrlData;

interface FailedUrlServiceInterface
{
    public function addFailedUrl(FailedUrlData $failedUrl): void;
    public function getFailedUrls(): array;
    public function clearFailedUrls(): void;
}