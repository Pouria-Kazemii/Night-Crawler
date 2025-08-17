<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model as EloquentModel;

class CrawlerResult extends EloquentModel
{
    protected $connection = 'mongodb';

    protected string $collection = 'crawler_results';

    public $timestamps = true;

    protected $primaryKey = '_id';


    protected $fillable = [
        'crawler_job_sender_id',
        'crawler_id',
        'final_url',
        'url',
        'content',
        'encrypt_url',
        'content_changed',
        'content_dif'
    ];

    public function crawlerJobSender()
    {
        return $this->belongsTo(CrawlerJobSender::class , 'crawler_job_sender_id','_id');
    }
}
