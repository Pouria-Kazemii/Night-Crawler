<?php
namespace App\Services\CountManagement\Exceptions;

use App\Services\CountManagement\Enums\CounterType;

class InvalidCounterTypeException extends \InvalidArgumentException
{
    public function __construct(string $type)
    {
        parent::__construct("Invalid counter type: {$type}. Valid types are: " . implode(', ', CounterType::values()));
    }
}