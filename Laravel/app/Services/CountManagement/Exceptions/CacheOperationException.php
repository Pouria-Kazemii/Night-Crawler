<?php
namespace App\Services\CountManagement\Exceptions;

class CacheOperationException extends \RuntimeException
{
    public function __construct(string $operation, string $key, ?\Throwable $previous = null)
    {
        parent::__construct("Cache operation '{$operation}' failed for key: {$key}", 0, $previous);
    }
}