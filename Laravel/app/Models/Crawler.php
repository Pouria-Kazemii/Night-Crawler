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
        'title', // string
        'description', //string
        'crawler_status',  // active | paused | completed | error
        'crawler_type', // static | dynamic | paginated | authenticated | api | seed 
        'base_url', // string
        'start_urls', // array
        'selectors', // objects
        'pagination_rule', // objects
        'auth', // object
        'api_config', // object
        'schedule', // objects
        'max_depth', // int
        'link_filter_rules', // array
        'crawl_delay', // int,
        'last_run_at' // datetime
    ];

    public function casts() : array
    {
        return [
            'start_urls' => 'array',
            'selectors' => 'array',
            'pagination_rule' => 'array',
            'auth' => 'array',
            'api_config' => 'array',
            'schedule' => 'array',
            'max_depth' => 'integer',
            'start_urls' => 'array',
            'link_filter_rules' => 'array',
            'last_run_at' => 'datetime'
        ];
    }
}
