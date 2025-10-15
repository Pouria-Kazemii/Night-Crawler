<?php
namespace App\Services\CountManagement\Exceptions;

class JobNotFoundException extends \RuntimeException
{
    public function __construct(string $jobId)
    {
        parent::__construct("Job with ID '{$jobId}' not found");
    }
}