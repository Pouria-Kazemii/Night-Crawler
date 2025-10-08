<?php
namespace App\Services\CountManagement\Enums;

enum JobStatus: string
{
    case SUCCESS = 'success';
    case FAILED = 'failed';
    case PROCESSING = 'processing';
    case QUEUED = 'queued';
}