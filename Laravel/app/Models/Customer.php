<?php

namespace App\Models;

use Carbon\Carbon;
use Hekmatinasser\Verta\Facades\Verta;
use Illuminate\Database\Eloquent\Casts\Attribute;
use MongoDB\Laravel\Eloquent\Model as EloquentModel;
use illuminate\Support\Str;

class Customer extends EloquentModel
{
    protected $connection = 'mongodb';

    protected string $collection = 'customers';

    public $timestamps = true;

    protected $primaryKey = '_id';


    protected $fillable = [
        'name',                     //sample name for customer
        'url',                      //url of the customer,
        'ip_address',               //ip address for more validations
        'token',                    //created token for usage
        'subscribe_uct',            //type of subscribe
        'token_used',               //used token count
        'expire_date',              //maximum date for use tokens
        'available_crawler_ids',    //array of crawler ids that customer have access to data
    ];

    protected function casts(): array
    {
        return [
            'name' => 'string',
            'url' => 'string',
            'ip_address' => 'string',
            'token' => 'string',
            'subscribe_uct' => 'string',
            'token_used' => 'integer',
            'expire_date' => 'date',
            'available_crawler_ids' => 'array'
        ];
    }

    protected function expireDateShamsi(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $attributes['expire_date'] 
                ? Verta::instance($attributes['expire_date'])->format('%B %d، %Y')
                : null,
        );
    }

    //relations
    public function subscribe()
    {
        return $this->hasOne(Subscribe::class, 'uct', 'subscribe_uct');
    }

    protected static function booted()
    {
        //auto generate token when customer create
        static::creating(function ($customer) {
            $customer->token = hash('sha256', Str::random(60));
        });
    }
}
