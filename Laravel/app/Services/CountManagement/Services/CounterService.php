<?php
namespace App\Services\CountManagement\Services;

use App\Services\CountManagement\Contracts\CounterServiceInterface;
use App\Services\CountManagement\Contracts\CacheRepositoryInterface;
use App\Services\CountManagement\Enums\CounterType;

class CounterService implements CounterServiceInterface
{
    public function __construct(
        private string $jobId,
        private CacheRepositoryInterface $cache,
        private array $defaultCounts = []
    ) {}

    public function increment(CounterType $type): int
    {
        $key = $this->getKey($type);
        return $this->cache->increment($key);
    }

    public function increments(CounterType $type , int $amount) : int
    {
        $key  = $this->getKey($type);
        return $this->cache->increment($key , $amount);
    }

    public function get(CounterType $type): int
    {
        $key = $this->getKey($type);
        return (int) $this->cache->get($key, $this->defaultCounts[$type->value] ?? 0);
    }

    public function getAll(): array
    {
        $counts = [];
        foreach (CounterType::cases() as $type) {
            $counts[$type->value] = $this->get($type);
        }
        return $counts;
    }

    public function reset(): void
    {
        foreach (CounterType::cases() as $type) {
            $this->cache->forget($this->getKey($type));
        }
    }

    private function getKey(CounterType $type): string
    {
        return "{$this->jobId}_{$type->value}_count";
    }
}