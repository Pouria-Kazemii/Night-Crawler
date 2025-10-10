<?php
namespace App\Providers;

use App\Services\CountManagement\Contracts\CacheRepositoryInterface;
use App\Services\CountManagement\Repositories\LaravelCacheRepository;
use App\Services\CountManagement\Services\ResultCountManagement;
use Illuminate\Support\ServiceProvider;

class CountManagementServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind repository
        $this->app->bind(CacheRepositoryInterface::class, LaravelCacheRepository::class);
        
        // Register the main service but require jobId to be passed explicitly
        $this->app->bind(ResultCountManagement::class, function () {
            throw new \Exception(
                "ResultCountManagement requires jobId. " .
                "Use ResultCountManagement::create('job-id') or " .
                "CountManagementServiceProvider::createForJob('job-id')"
            );
        });
    }

    /**
     * Factory method to create service for specific job
     */
    public static function createForJob(string $jobId, array $defaultCounts = []): ResultCountManagement
    {
        $cacheRepo = app(CacheRepositoryInterface::class);
        
        return ResultCountManagement::create($jobId, $cacheRepo, $defaultCounts);
    }
}