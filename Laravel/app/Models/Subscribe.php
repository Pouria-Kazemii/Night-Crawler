<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model as EloquentModel;
use illuminate\Support\Str;

class Subscribe extends EloquentModel
{
    protected $connection = 'mongodb';

    protected string $collection = 'subscribes';

    public $timestamps = true;

    protected $primaryKey = '_id';

    protected $fillable = [
        'title',                    //title of subscribe
        'uct',                      //unique code according time and type => for example g-1 -> g stand for gold tier and 1 month || b-6 bronze tier and 6 month   
        'available_token_count',    //number of tokens that customer can use
        'expired_time',             //after how many time subscribe end (1month-3Month-6month-1year)
    ];
}
