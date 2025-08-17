<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model as EloquentModel;

class CrawlerJobReceiver extends EloquentModel
{
    protected $connection = 'mongodb';

    protected string $collection = 'crawler_job_receivers';

    public $timestamps = true;

    protected $primaryKey = '_id';


    protected $fillable = [
        'crawler_sender_id',
        'node_id',
        'urls',
        'status',               // success || running || failed
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
        return $this->belongsTo(Crawler::class, 'crawler_id', '_id');
    }

    public function crawlerResults()
    {
        return $this->hasMany(CrawlerResult::class, 'job_id', '_id');
    }
}
