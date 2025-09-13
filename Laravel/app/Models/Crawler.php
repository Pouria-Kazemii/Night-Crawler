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
        'title',                 // string
        'description',           // string
        'crawler_status',        // active | completed | error | running | pause | first_step_done
        'crawler_type',          // static | dynamic | paginated | authenticated | api | seed | two_step
        'base_url',              // string
        'start_urls',            // array
        'url_pattern',           // string
        'range',                 // array
        'pagination_rule',       // array (JSON)
        'auth',                  // array (JSON)
        'api_config',            // array (JSON)
        'dynamic_limit',         // integer
        'schedule',              // integer (minute)
        'link_filter_rules',     // array (only for seed)
        'crawl_delay',           // integer
        'last_run_at',           // datetime
        'selectors',             // array (JSON)
        'link_selector',         // string
        'two_step',              // array(JSON)
        'next_run_at',           // datetime
        'array_selector'         // boolean
    ];

    public function casts(): array
    {
        return [
            'start_urls'        => 'array',
            'url_pattern'       => 'string',
            'range'             => 'array',
            'pagination_rule'   => 'array',
            'auth'              => 'array',
            'api_config'        => 'array',
            'schedule'          => 'integer',
            'selectors'         => 'array',
            'link_selector'     => 'string',
            'link_filter_rules' => 'array',
            'crawl_delay'       => 'integer',
            'two_step'          => 'array',
            'last_run_at'       => 'datetime',
            'next_run_at'       => 'datetime',
        ];
    }

    protected static function booted()
    {
        static::saving(function ($crawler) {
            // If schedule is set (not null/0) and was changed (or new record)
            if (!empty($crawler->schedule) && $crawler->schedule > 0 && $crawler->isDirty('schedule')) {
                $crawler->next_run_at = Carbon::now('UTC')->addMinutes((int)$crawler->schedule);
            }
        });
    }

    public function scopeWithNotProcessedSender($query)
    {
        return $query->with(['crawlerJobSender' => function ($q) {
            $q->where('processed', false);
        }]);
    }

    public function scopeWithQueuedOrRunningSender($query)
    {
        return $query->with(['crawlerJobSender' => function ($q) {
            $q->where(function ($query) {
                $query->where('status', 'running')
                    ->orWhere('status', 'queued');
            });
        }]);
    }

    public function crawlerResult()
    {
        return $this->hasMany(CrawlerResult::class, 'crawler_id', '_id');
    }

    public function crawlerJobSender()
    {
        return $this->hasMany(CrawlerJobSender::class, 'crawler_id', '_id');
    }
}
