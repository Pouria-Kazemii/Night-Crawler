<?php
namespace App\Services\CountManagement\DTOs;

use App\Services\CountManagement\Enums\JobStatus;

class StatusData
{
    public function __construct(
        public readonly string $jobId,
        public readonly JobStatus $status,
        public readonly ?string $updatedAt = null
    ) {}
}