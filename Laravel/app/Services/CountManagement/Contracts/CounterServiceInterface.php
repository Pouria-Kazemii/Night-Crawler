<?php
namespace App\Services\CountManagement\Contracts;

use App\Services\CountManagement\Enums\CounterType;

interface CounterServiceInterface
{
    public function increment(CounterType $type): int;
    public function increments(CounterType $type , int $amount):int;
    public function get(CounterType $type): int;
    public function getAll(): array;
    public function reset(): void;
}