<?php
namespace App\Services\CountManagement\Contracts;

interface CacheRepositoryInterface
{
    public function get(string $key, $default = null);
    public function set(string $key, $value, ?int $ttl = null): void;
    public function forever(string $key, $value): void;
    public function forget(string $key): void;
    public function increment(string $key, int $amount = 1): int;
}