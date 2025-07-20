<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model as EloquentModel;

class CrawlerJob extends EloquentModel
{
    protected $connection = 'mongodb';

    protected string $collection = 'crawler_jobs';

    public $timestamps = true;

    protected $primaryKey = '_id';


    protected $fillable = [
        'crawler_id',
        'node_id',
        'urls',
        'status',
        'started_at',
        'completed_at',
        'retries',
        'failed_url',
        'last_used_at',
    ];

    protected $casts = [
        'urls' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'last_used_at' => 'datetime'
    ];

    public function crawler()
    {
        return $this->belongsTo(Crawler::class , 'crawler_id' , '_id');
    }
}
