<?php
namespace App\Services\CountManagement\DTOs;

class FailedUrlData
{
    public function __construct(
        public readonly string $url,
        public readonly ?string $error = null,
        public readonly ?int $statusCode = null,
        public readonly ?string $timestamp = null
    ) {}
}