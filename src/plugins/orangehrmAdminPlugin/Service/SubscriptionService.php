<?php

namespace OrangeHRM\Admin\Service;

use Doctrine\ORM\NonUniqueResultException;
use OrangeHRM\Admin\Dao\SubscriptionDao;
use OrangeHRM\Admin\Dao\UserDao;
use OrangeHRM\Entity\Subscription;
use OrangeHRM\Entity\User;

class SubscriptionService
{
    private ?SubscriptionDao $subscriptionDao = null;
    private ?UserDao $userDao = null;

    protected const WHITELIST_EMAILS = [
        'justice+starsteels@crossjobs.co',
        //'johnmaxwelljeon@gmail.com',
        //'dadzieemmanuel94@gmail.com',
    ];


    /**
     * @throws NonUniqueResultException
     */
    public function getActiveSubscription(?int $userId = null): ?Subscription
    {
        if ($userId) {
            $user = $this->getUserDao()->getSystemUser($userId);

            if (in_array($user?->getUserName(), self::WHITELIST_EMAILS)) {
                return new Subscription();
            }
        }

        return $this->getSubscriptionDao()->getActiveSubscription();
    }

    public function getActiveSubscriptionWithOrgId(int $orgId, ?int $userId = null): ?Subscription
    {
        if ($userId) {
            $user = $this->getUserDao()->getSystemUser($userId);

            if (in_array($user?->getUserName(), self::WHITELIST_EMAILS)) {
                return new Subscription();
            }
        }
        return $this->getSubscriptionDao()->getActiveSubscriptionWithOrgId($orgId);
    }

    public function getSubscriptionDao(): SubscriptionDao
    {
        if (!($this->subscriptionDao instanceof SubscriptionDao)) {
            $this->subscriptionDao = new SubscriptionDao();
        }

        return $this->subscriptionDao;
    }

    public function getUserDao(): UserDao
    {
        if (!($this->userDao instanceof UserDao)) {
            $this->userDao = new UserDao();
        }

        return $this->userDao;
    }
}