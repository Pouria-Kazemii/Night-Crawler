<?php

namespace App\Services\CountManagement\Services;

use App\Services\CountManagement\Contracts\CounterServiceInterface;
use App\Services\CountManagement\Contracts\StatusServiceInterface;
use App\Services\CountManagement\Contracts\FailedUrlServiceInterface;
use App\Services\CountManagement\Contracts\CacheRepositoryInterface;
use App\Services\CountManagement\Enums\CounterType;
use App\Services\CountManagement\Enums\JobStatus;
use App\Services\CountManagement\DTOs\FailedUrlData;
use App\Services\CountManagement\DTOs\CounterData;

class ResultCountManagement
{
    private string $jobId;

    public function __construct(
        string $jobId,
        private CounterServiceInterface $counterService,
        private StatusServiceInterface $statusService,
        private FailedUrlServiceInterface $failedUrlService
    ) {
        $this->jobId = $jobId;
    }

    /**
     * Factory method to create service for specific job
     */
    public static function create(
        string $jobId,
        CacheRepositoryInterface $cacheRepo,
        array $defaultCounts = []
    ): self {
        $counterService = new CounterService($jobId, $cacheRepo, $defaultCounts);
        $statusService = new StatusService($jobId, $cacheRepo);
        $failedUrlService = new FailedUrlService($jobId, $cacheRepo);

        return new self($jobId, $counterService, $statusService, $failedUrlService);
    }

    // Counter methods
    public function incrementSuccess(): int
    {
        return $this->counterService->increment(CounterType::SUCCESS);
    }

    public function incrementsSuccess(int $amount): int
    {
        return $this->counterService->increments(CounterType::SUCCESS, $amount);
    }

    public function incrementRepeated(): int
    {
        return $this->counterService->increment(CounterType::REPEATED);
    }

    public function incrementsRepeated(int $amount): int
    {
        return $this->counterService->increments(CounterType::REPEATED, $amount);
    }

    public function incrementChanged(): int
    {
        return $this->counterService->increment(CounterType::CHANGED);
    }

    public function incrementsChanged(int $amount): int
    {
        return $this->counterService->increments(CounterType::CHANGED, $amount);
    }

    public function incrementNotChanged(): int
    {
        return $this->counterService->increment(CounterType::NOT_CHANGED);
    }

    public function incrementsNotChanged(int $amount): int
    {
        return $this->counterService->increments(CounterType::NOT_CHANGED, $amount);
    }

    // Status methods
    public function markAsFailed(): void
    {
        $this->statusService->setStatus(JobStatus::FAILED);
    }

    public function markAsSuccess(): void
    {
        $this->statusService->setStatus(JobStatus::SUCCESS);
    }

    public function markAsProcessing(): void
    {
        $this->statusService->setStatus(JobStatus::PROCESSING);
    }

    public function getCurrentStatus(): ?JobStatus
    {
        return $this->statusService->getStatus();
    }

    // Failed URL methods
    public function addFailedUrl(string $url, ?string $error = null, ?int $statusCode = null): void
    {
        $failedUrl = new FailedUrlData(
            url: $url,
            error: $error,
            statusCode: $statusCode
        );

        $this->failedUrlService->addFailedUrl($failedUrl);
    }

    public function getFailedUrls(): array
    {
        return $this->failedUrlService->getFailedUrls();
    }

    // Data aggregation
    public function getAllData(): CounterData
    {
        return new CounterData(
            jobId: $this->jobId,
            counts: $this->counterService->getAll(),
            status: $this->statusService->getStatus() ?? JobStatus::SUCCESS,
            failedUrls: $this->failedUrlService->getFailedUrls(),
        );
    }

    // Get individual counts
    public function getSuccessCount(): int
    {
        return $this->counterService->get(CounterType::SUCCESS);
    }

    public function getRepeatedCount(): int
    {
        return $this->counterService->get(CounterType::REPEATED);
    }

    public function getChangedCount(): int
    {
        return $this->counterService->get(CounterType::CHANGED);
    }

    public function getNotChangedCount(): int
    {
        return $this->counterService->get(CounterType::NOT_CHANGED);
    }

    // Cleanup
    public function cleanup(): void
    {
        $this->counterService->reset();
        $this->failedUrlService->clearFailedUrls();
        $this->statusService->clearStatus();
    }

    public function getJobId(): string
    {
        return $this->jobId;
    }
}
