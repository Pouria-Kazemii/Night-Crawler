<?php

namespace App\Services\Subscribe\Services;

use App\Models\Customer;

class SubscribeToken
{
    public function haveToken(Customer $customer) : bool
    {
        $maxToken = $this->tokenCount($customer) ;
        
        return $this->isUnlimit($maxToken) ? 
        true :
        $customer->token_used < $maxToken;
    }

    public function tokenCount(Customer $customer) : int
    {
        return $customer->load('subscribe')->subscribe->available_token_count;
    }

    public function isUnlimit(mixed $value) : bool
    {
        return $value == 'unlimit' ? true : false;
    }
}