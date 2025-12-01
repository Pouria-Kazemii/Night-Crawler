<?php

namespace App\Services\CustomerServices\Subscribe\Repositories;

use App\Services\CustomerServices\Subscribe\Contracts\Abstracts\BaseCacheRepo;

class CustomerSubscriptionRepo extends BaseCacheRepo
{
    public function __construct()
    {
        parent::__construct('customers', 86400); // 24 hours TTL
    }

    public function setSubscriptionStatus(string $customerId, bool $isSubscribed): bool
    {
        return $this->put("{$customerId}:sub", $isSubscribed);
    }

    public function getSubscriptionStatus(string $customerId): bool
    {
        return $this->get("{$customerId}:sub", false);
    }

    public function hasSubscription(string $customerId): bool
    {
        return $this->has("{$customerId}:sub");
    }

    public function removeSubscriptionStatus(string $customerId): bool
    {
        return $this->forget("{$customerId}:sub");
    }

    public function setMultipleSubscriptions(array $subscriptions): void
    {
        foreach ($subscriptions as $customerId => $isSubscribed) {
            $this->setSubscriptionStatus($customerId, $isSubscribed);
        }
    }

    public function getSubscribedCustomerIds(array $customerIds): array
    {
        $subscribed = [];

        foreach ($customerIds as $customerId) {
            if ($this->getSubscriptionStatus($customerId)) {
                $subscribed[] = $customerId;
            }
        }

        return $subscribed;
    }
}
