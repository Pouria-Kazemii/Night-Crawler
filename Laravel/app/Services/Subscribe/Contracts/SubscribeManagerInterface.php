<?php

namespace App\Services\CustomerServices\Subscribe\Contracts;

use App\Models\Customer;

interface SubscribeManagerInterface
{
    public function assignSubscription(Customer $customer , string $subscribeUct): bool;
}