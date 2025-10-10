<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model as EloquentModel;

class CrawlerJobSender extends EloquentModel
{
    protected $connection = 'mongodb';

    protected string $collection = 'crawler_job_senders';

    public $timestamps = true;

    protected $primaryKey = '_id';

    protected $fillable = [
        'crawler_id',
        'node_id',
        'urls',
        'status',                 // success || running || failed || queued
        'step',                  // 0 => default || 1 => first step of two step crawler || 2 => second step of two step crawler
        'retries',
        'payload',
        'failed_url',
        'processed',
        'last_used_at',
        'started_at',
        'completed_at',
        'counts',
        'status_priority'
    ];


    public function casts(): array
    {
        return [
            'failed_urls' => 'array',
            'counts' => 'array',
            'urls' => 'array',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'last_used_at' => 'datetime',
            'status_priority' => 'integer',
        ];
    }

    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $priorityMap = [
                'running' => 1,
                'queued' => 2,
                'failed' => 3,
                'success' => 4
            ];

            $model->status_priority = $priorityMap[$model->status] ?? 5;
        });
    }

    public function scopeOrderByStatusPriority($query)
    {
        return $query->orderBy('status_priority')->orderByDesc('last_used_at');
    }

    public function scopeGetLastQueued($query, $node_id)
    {
        return $query->where('node_id', $node_id)->where('status', 'queued')->orderBy('updated_at');
    }

    public function lastResult()
    {
        return $this->hasOne(CrawlerResult::class)->latestOfMany();
    }

    public function crawler()
    {
        return $this->belongsTo(Crawler::class, 'crawler_id', '_id');
    }

    public function crawlerResults()
    {
        return $this->hasMany(CrawlerResult::class, 'crawler_job_sender_id', '_id');
    }

    public function crawlerNode()
    {
        return $this->belongsTo(CrawlerNode::class, 'node_id', '_id');
    }
}
