<?php

namespace App\Services\CustomerServices\Subscribe\Repository;

use App\Models\Customer;
use DateTime;
use Illuminate\Support\Facades\Log;

class CustomerRepo
{
    public function updateSubscribe(Customer $customer, string $subscribeUct, DateTime $expireDate): bool
    {
        try {
            return $customer->update([
                'subscribe_uct' => $subscribeUct,
                'token_used' => 0 ,
                'expire_date' => $expireDate,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to update subscription', [
                'customer_id' => $customer->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}