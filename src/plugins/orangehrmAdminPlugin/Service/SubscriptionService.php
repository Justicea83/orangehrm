<?php

namespace OrangeHRM\Admin\Service;

use Doctrine\ORM\NonUniqueResultException;
use OrangeHRM\Admin\Dao\SubscriptionDao;
use OrangeHRM\Entity\Subscription;

class SubscriptionService
{
    private ?SubscriptionDao $subscriptionDao = null;

    /**
     * @throws NonUniqueResultException
     */
    public function getActiveSubscription(): ?Subscription
    {
        return $this->getSubscriptionDao()->getActiveSubscription();
    }

    public function getActiveSubscriptionWithOrgId(int $orgId): ?Subscription
    {
        return $this->getSubscriptionDao()->getActiveSubscriptionWithOrgId($orgId);
    }

    public function getSubscriptionDao(): SubscriptionDao
    {
        if (!($this->subscriptionDao instanceof SubscriptionDao)) {
            $this->subscriptionDao = new SubscriptionDao();
        }

        return $this->subscriptionDao;
    }
}