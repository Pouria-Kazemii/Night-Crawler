<?php

namespace App\Services\CountManagement\Services;

use App\Services\CountManagement\Contracts\StatusServiceInterface;
use App\Services\CountManagement\Contracts\CacheRepositoryInterface;
use App\Services\CountManagement\Enums\JobStatus;

class StatusService implements StatusServiceInterface
{
    public function __construct(
        private string $jobId,
        private CacheRepositoryInterface $cache
    ) {}

    public function setStatus(JobStatus $status): void
    {
        $this->cache->forever("{$this->jobId}_status", $status->value);
    }

    public function getStatus(): ?JobStatus
    {
        $status = $this->cache->get("{$this->jobId}_status");
        return $status ? JobStatus::from($status) : null;
    }

    public function clearStatus(): void
    {
        $this->cache->forget("{$this->jobId}_status");
    }
}
