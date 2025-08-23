<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model as EloquentModel;

class CrawlerNode extends EloquentModel
{
    protected $connection = 'mongodb';

    protected string $collection = 'crawler_node';

    public $timestamps = true;

    protected $primaryKey = '_id';

    protected $fillable = [
        'name',
        'ip_address',
        'port',
        'protocol', //http, https, socks5
        'status', //active, inactive, banned, down
        'last_used_at',
        'is_verified',
        'location',
        'latency',
    ];

    public function casts(): array
    {
        return [
            'is_verifyed' => 'boolean',
            'last_used_at' => 'datetime'
        ];
    }

    public function scopeSortedActive($query)
    {
        return $query
            ->where('status', 'active')
            ->whereNotNull('latency');
    }
    
    public function crawlerJobSender()
    {
        return $this->hasMany(CrawlerJobSender::class, 'node_id', '_id');
    }
}
