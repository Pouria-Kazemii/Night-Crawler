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
        'job_id',
        'final_url',
        'url',
        'content',
        'encrypt_url',
    ];
}
