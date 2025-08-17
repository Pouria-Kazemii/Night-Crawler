<?php

namespace App\Models;

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
        'two_step'               // array(JSON)
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
            'schedule'          => 'array',
            'selectors'         => 'array',
            'link_selector'     => 'string',
            'link_filter_rules' => 'array',
            'crawl_delay'       => 'integer',
            'two_step'          => 'array',
            'last_run_at'       => 'datetime',
        ];
    }

    public function scopeWithFilteredResultsByStep($query, int $step = 1)
    {
        return $query->whereHas('crawlerJobSender', function ($q) use ($step) {
            $q->where('step', $step);
        })->with(['crawlerResult' => function ($q) {
            $q->where('content_changed', true);
        }]);
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
