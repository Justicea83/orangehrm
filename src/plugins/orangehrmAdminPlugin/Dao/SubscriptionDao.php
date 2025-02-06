<?php

namespace OrangeHRM\Admin\Dao;

use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\Subscription;

class SubscriptionDao extends BaseDao
{
    /**
     * @throws NonUniqueResultException
     * @throws \DateMalformedStringException
     */
    public function getActiveSubscription(): ?Subscription
    {
        $qb = $this->getRepository(Subscription::class)->createQueryBuilder('s');

        $qb->where(
            $qb->expr()->orX(
                $qb->expr()->andX(
                    $qb->expr()->isNotNull('s.currentPeriodEnd'),
                    $qb->expr()->gt('s.currentPeriodEnd', ':now')
                ),
                $qb->expr()->andX(
                    $qb->expr()->isNull('s.currentPeriodEnd'),
                    $qb->expr()->eq('s.status', ':status')
                )
            )
        )
            ->setParameter('status', 'active')
            ->setParameter('now', new \DateTime());

        return $qb->getQuery()->getOneOrNullResult();
    }
}