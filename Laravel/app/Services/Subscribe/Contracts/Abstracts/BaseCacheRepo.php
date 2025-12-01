<?php

namespace App\Services\CustomerServices\Subscribe\Contracts\Abstracts;

use Illuminate\Support\Facades\Cache;

abstract class BaseCacheRepo
{
    protected string $prefix;
    protected int $defaultTtl;

    public function __construct(string $prefix, int $defaultTtl = 3600)
    {
        $this->prefix = $prefix;
        $this->defaultTtl = $defaultTtl;
    }

    protected function key(string $key): string
    {
        return "{$this->prefix}:{$key}";
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return Cache::get($this->key($key), $default);
    }

    public function put(string $key, mixed $value, ?int $ttl = null): bool
    {
        return Cache::put($this->key($key), $value, $ttl ?? $this->defaultTtl);
    }

    public function remember(string $key, \Closure $callback, ?int $ttl = null): mixed
    {
        return Cache::remember($this->key($key), $ttl ?? $this->defaultTtl, $callback);
    }

    public function forget(string $key): bool
    {
        return Cache::forget($this->key($key));
    }

    public function has(string $key): bool
    {
        return Cache::has($this->key($key));
    }
}