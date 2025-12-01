<?php

namespace App\Services\Subscribe\Services;

use App\Models\Customer;
use App\Services\CustomerServices\Subscribe\Contracts\SubscribeManagerInterface;
use App\Services\CustomerServices\Subscribe\Repositories\CustomerSubscriptionRepo;
use App\Services\CustomerServices\Subscribe\Repository\CustomerRepo;

class SubscribeManager implements SubscribeManagerInterface
{
    public function __construct(
        private readonly CalculateSubscribeDate $date,
        private readonly CustomerRepo $customerRepo,
        private readonly SubscribeToken $token,
        private readonly CustomerSubscriptionRepo $customerSubscriptionRepo
    ) {}

    public function checkSubscription(Customer $customer): bool
    {
        if ($this->customerSubscriptionRepo->getSubscriptionStatus($customer->id)) {
            return true;
        } else {
            $haveSub = (!$this->date->checkExpired($customer) &&  $this->token->haveToken($customer));

            $this->customerSubscriptionRepo->setSubscriptionStatus($customer->id, $haveSub);

            return $haveSub;
        }
    }

    public function assignSubscription(Customer $customer, string $subscribeUct): bool
    {
        $expireDate = $this->date->expireDate($subscribeUct);
        return $this->customerRepo->updateSubscribe($customer, $subscribeUct, $expireDate);
    }
}
