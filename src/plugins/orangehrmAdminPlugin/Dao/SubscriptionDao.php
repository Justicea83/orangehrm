<?php

namespace OrangeHRM\Admin\Dao;

use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use OrangeHRM\Admin\entity\Subscription;
use OrangeHRM\Core\Dao\BaseDao;

class SubscriptionDao extends BaseDao
{
    /**
     * @throws NonUniqueResultException
     */
    public function getActiveSubscription(): ?Subscription
    {
        $qb = $this->getRepository(Subscription::class)->createQueryBuilder('s');

        $qb->where(
            $qb->expr()->orX(
                $qb->expr()->eq('s.status', ':status'),
                $qb->expr()->gt('s.currentPeriodEnd', ':now')
            )
        )
            ->setParameter('status', 'active')
            ->setParameter('now', new DateTime());

        return $qb->getQuery()->getOneOrNullResult();
    }
}