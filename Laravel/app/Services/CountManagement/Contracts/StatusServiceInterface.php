<?php

namespace App\Services\CountManagement\Contracts;

use App\Services\CountManagement\Enums\JobStatus;

interface StatusServiceInterface
{
    public function setStatus(JobStatus $status): void;
    public function getStatus(): ?JobStatus;
    public function clearStatus(): void;
}
