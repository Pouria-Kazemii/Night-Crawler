<?php

namespace App\Models;

use Carbon\Carbon;
use MongoDB\Laravel\Eloquent\Model as EloquentModel;

class Crawler extends EloquentModel
{
    protected $connection = 'mongodb';

    protected string $collection = 'crawlers';

    public $timestamps = true;

    protected $primaryKey = '_id';

    protected $fillable = [
        'title',                   // string
        'description',             // string
        'crawler_status',          // active | completed | error | running | pause | first_step_done
        'crawler_type',            // static | dynamic | paginated | authenticated | api | seed | two_step
        'crawler_priority',        // integer (in 1 and 2 and 3)
        'base_url',                // string
        'start_urls',              // array
        'url_pattern',             // string
        'range',                   // array
        'upgrade_range',           // array
        'pagination_rule',         // array (JSON)
        'auth',                    // array (JSON)
        'api_config',              // array (JSON)
        'dynamic_limit',           // integer
        'schedule',                // array 
        'link_filter_rules',       // array (only for seed)
        'crawl_delay',             // integer
        'crawl_delay_second_step', // integer
        'last_run_at',             // datetime
        'update_selectors',        // array (JSON)
        'selectors',               // array (JSON)
        'link_selector',           // string
        'two_step',                // array(JSON)
        'next_update_run_at',      // datetime
        'next_upgrade_run_at',     // datetime
        'array_selector',          // boolean
    ];

    public function casts(): array
    {
        return [
            'start_urls'              => 'array',
            'url_pattern'             => 'string',
            'schedule'                => 'array',
            'range'                   => 'array',
            'pagination_rule'         => 'array',
            'upgrade_range'           => 'array',
            'auth'                    => 'array',
            'api_config'              => 'array',
            'selectors'               => 'array',
            'link_selector'           => 'string',
            'link_filter_rules'       => 'array',
            'crawl_delay'             => 'integer',
            'crawl_delay_second_step' => 'integer',
            'two_step'                => 'array',
            'last_run_at'             => 'datetime',
            'next_update_run_at'      => 'datetime',
            'next_upgrade_run_at'     => 'datetime',
            'update_selectors'        => 'array',
            'crawler_priority'        => 'integer',
        ];
    }

    protected static function booted()
    {

        // Auto save next_upgrade and nex_update timestamps 
        static::saving(function ($crawler) {
            $original = $crawler->getOriginal('schedule') ?? [];
            $current = $crawler->schedule ?? [];

            $originalUpdate = $original['update'] ?? null;
            $currentUpdate = $current['update'] ?? null;

            if (!empty($currentUpdate) && $currentUpdate > 0 && $currentUpdate != $originalUpdate) {
                $crawler->next_update_run_at = now('UTC')->addMinutes((int) $currentUpdate);
            }

            $originalUpgrade = $original['upgrade'] ?? null;
            $currentUpgrade = $current['upgrade'] ?? null;

            if (!empty($currentUpgrade) && $currentUpgrade > 0 && $currentUpgrade != $originalUpgrade) {
                $crawler->next_upgrade_run_at = now('UTC')->addMinutes((int) $currentUpgrade);
            }
        });

        static::deleted(function ($crawler){

            $crawler->crawlerJobSender()->each(function($sender){
                $sender->delete();
            });

            $crawler->crawlerResult()->each(function($result){
                $result->delete();
            });
            
        });
    }

    // Scope for getting with not processed CrawlerJobSender
    public function scopeWithNotProcessedSender($query)
    {
        return $query->with(['crawlerJobSender' => function ($q) {
            $q->where('processed', false);
        }]);
    }

    // Scope for getting with running or queued CrawlerJobSender
    public function scopeWithQueuedOrRunningSender($query)
    {
        return $query->with(['crawlerJobSender' => function ($q) {
            $q->where(function ($query) {
                $query->where('status', 'running')
                    ->orWhere('status', 'queued');
            });
        }]);
    }

    // Relations
    public function crawlerResult()
    {
        return $this->hasMany(CrawlerResult::class, 'crawler_id', '_id');
    }

    public function crawlerJobSender()
    {
        return $this->hasMany(CrawlerJobSender::class, 'crawler_id', '_id');
    }
}
