<?php

namespace OrangeHRM\Onboarding\Dao;

use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\TaskGroup;
use OrangeHRM\Onboarding\Dto\TaskGroupSearchFilterParams;
use OrangeHRM\ORM\ListSorter;
use OrangeHRM\ORM\QueryBuilderWrapper;

class TaskGroupDao extends BaseDao
{
    public function getTaskGroupList(TaskGroupSearchFilterParams $filterParams): array
    {
        $qb = $this->getTaskGroupListQueryBuilderWrapper($filterParams)->getQueryBuilder();
        return $qb->getQuery()->execute();
    }

    public function getTaskGroupListCount(TaskGroupSearchFilterParams $filterParams): int
    {
        $qb = $this->getTaskGroupListQueryBuilderWrapper($filterParams)->getQueryBuilder();
        return $this->getPaginator($qb)->count();
    }

    protected function getTaskGroupListQueryBuilderWrapper(TaskGroupSearchFilterParams $filterParams): QueryBuilderWrapper
    {
        $q = $this->createQueryBuilder(TaskGroup::class, 't');
        $q->leftJoin('t.task', 'task');
        $q->distinct();

        if (!is_null($filterParams->getGroupAssignmentId())) {
            $q->andWhere('t.groupAssignmentId = :groupAssignmentId');
            $q->setParameter('groupAssignmentId', $filterParams->getGroupAssignmentId());
        }

        $this->setSortingAndPaginationParams($q, $filterParams);

        $q->orderBy('t.id', ListSorter::DESCENDING);

        return $this->getQueryBuilderWrapper($q);
    }
}