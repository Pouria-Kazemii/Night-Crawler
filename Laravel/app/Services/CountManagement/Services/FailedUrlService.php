<?php
namespace App\Services\CountManagement\Services;

use App\Services\CountManagement\Contracts\FailedUrlServiceInterface;
use App\Services\CountManagement\Contracts\CacheRepositoryInterface;
use App\Services\CountManagement\DTOs\FailedUrlData;

class FailedUrlService implements FailedUrlServiceInterface
{
    public function __construct(
        private string $jobId,
        private CacheRepositoryInterface $cache
    ) {}

    public function addFailedUrl(FailedUrlData $failedUrl): void
    {
        $key = "{$this->jobId}_failed_urls";
        $failedUrls = $this->cache->get($key, []);
        
        $failedUrls[] = [
            'url' => $failedUrl->url,
            'error' => $failedUrl->error,
            'status_code' => $failedUrl->statusCode,
            'timestamp' => $failedUrl->timestamp ?? now()->toISOString()
        ];
        
        $this->cache->forever($key, $failedUrls);
    }

    public function getFailedUrls(): array
    {
        return $this->cache->get("{$this->jobId}_failed_urls", []);
    }

    public function clearFailedUrls(): void
    {
        $this->cache->forget("{$this->jobId}_failed_urls");
    }
}