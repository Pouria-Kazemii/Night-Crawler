<?php
namespace App\Services\CountManagement\DTOs;

use App\Services\CountManagement\Enums\JobStatus;

class CounterData
{
    public function __construct(
        public readonly string $jobId,
        public readonly array $counts,
        public readonly JobStatus $status,
        public readonly array $failedUrls = [],
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null
    ) {}
}