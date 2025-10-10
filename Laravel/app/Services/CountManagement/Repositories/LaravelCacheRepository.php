<?php
namespace App\Services\CountManagement\Repositories;

use App\Services\CountManagement\Contracts\CacheRepositoryInterface;
use App\Services\CountManagement\Exceptions\CacheOperationException;
use Illuminate\Support\Facades\Cache;

class LaravelCacheRepository implements CacheRepositoryInterface
{
    public function get(string $key, $default = null)
    {
        try {
            return Cache::get($key, $default);
        } catch (\Exception $e) {
            throw new CacheOperationException('get', $key, $e);
        }
    }

    public function set(string $key, $value, ?int $ttl = null): void
    {
        try {
            Cache::put($key, $value, $ttl);
        } catch (\Exception $e) {
            throw new CacheOperationException('set', $key, $e);
        }
    }

    public function forever(string $key, $value): void
    {
        try {
            Cache::forever($key, $value);
        } catch (\Exception $e) {
            throw new CacheOperationException('forever', $key, $e);
        }
    }

    public function forget(string $key): void
    {
        try {
            Cache::forget($key);
        } catch (\Exception $e) {
            throw new CacheOperationException('forget', $key, $e);
        }
    }

    public function increment(string $key, int $amount = 1): int
    {
        try {
            return Cache::increment($key, $amount);
        } catch (\Exception $e) {
            throw new CacheOperationException('increment', $key, $e);
        }
    }
}