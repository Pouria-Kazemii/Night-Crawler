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
        'crawler_status',        // active | paused | completed | error
        'crawler_type',          // static | dynamic | paginated | authenticated | api | seed
        'base_url',              // string
        'start_urls',            // array
        'url_pattern',           // string 
        'range',                 // array 
        'pagination_rule',       // array (JSON)
        'auth',                  // array (JSON)
        'api_config',            // array (JSON)
        'schedule',              // array (JSON)
        'max_depth',             // integer (only for seed)
        'link_filter_rules',     // array (only for seed)
        'crawl_delay',           // integer
        'last_run_at',           // datetime
        'selectors',             // array (JSON)
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
            'max_depth'         => 'integer',
            'link_filter_rules' => 'array',
            'crawl_delay'       => 'integer',
            'last_run_at'       => 'datetime',
        ];
    }

    public function crawlerJobs()
    {
        return $this->hasMany(CrawlerJob::class, 'crawler_id', '_id');
    }
}
