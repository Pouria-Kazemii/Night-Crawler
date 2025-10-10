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
        'content',             //array or object
        'encrypt_url',         //string
        'content_upgrade',     //bool
        'content_update',      //bool
        'content_difference',  //array or object
    ];

    public function crawlerJobSender()
    {
        return $this->belongsTo(CrawlerJobSender::class , 'crawler_job_sender_id','_id');
    }

    public function crawler()
    {
        return $this->belongsTo(Crawler::class , 'crawler_id' , '_id');
    }

}
