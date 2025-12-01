<?php

namespace App\Services\Subscribe\Services;

use App\Models\Customer;
use Carbon\Carbon;
use DateTime;
use InvalidArgumentException;

class CalculateSubscribeDate
{

    public function expireDate(string $subscribeUtc): DateTime
    {
        $monthCount = $this->splitText($subscribeUtc);

        if ($monthCount <= 0) {
            throw new InvalidArgumentException("Invalid subscription month count: {$monthCount}");
        }

        return $this->addMonth($monthCount);
    }

    public function checkExpired(Customer $customer): bool
    {
        return $customer->expire_date < now();
    }

    public function addMonth(int $month): DateTime
    {
        return Carbon::now()->addMonth($month)->toDateTime();
    }

    public function splitText(string $subscribeUtc): int
    {
        preg_match('/\d+/', $subscribeUtc, $matches);

        return (int) $matches[0];
    }
}
