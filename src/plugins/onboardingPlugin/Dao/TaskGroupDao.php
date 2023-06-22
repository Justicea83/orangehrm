<?php

namespace OrangeHRM\Onboarding\Dao;

use Exception;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\GroupAssignment;
use OrangeHRM\Entity\TaskGroup;
use OrangeHRM\Onboarding\Dto\TaskGroupSearchFilterParams;
use OrangeHRM\Onboarding\Traits\Service\GroupAssignmentServiceTrait;
use OrangeHRM\ORM\ListSorter;
use OrangeHRM\ORM\QueryBuilderWrapper;

class TaskGroupDao extends BaseDao
{
    use GroupAssignmentServiceTrait;

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

    /**
     * @throws DaoException
     */
    public function deleteTaskGroupById(array $ids): int
    {
        try {
            $q = $this->createQueryBuilder(TaskGroup::class, 't');
            $q->delete()
                ->where($q->expr()->in('t.id', ':ids'))
                ->setParameter('ids', $ids);
            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    public function saveTaskGroup(TaskGroup $taskGroup): TaskGroup
    {
        $this->persist($taskGroup);
        return $taskGroup;
    }

    /**
     * @throws DaoException
     */
    public function toggleTaskGroupComplete(int $groupAssignmentId, int $taskGroupId, bool $status): int
    {
        try {
            $q = $this->createQueryBuilder(TaskGroup::class, 't');
            $q->update()
                ->set('t.completed', ':status')
                ->setParameter('status', $status)
                ->andWhere($q->expr()->eq('t.id', ':taskGroupId'))
                ->andWhere($q->expr()->eq('t.groupAssignmentId', ':groupAssignmentId'))
                ->setParameter('taskGroupId', $taskGroupId)
                ->setParameter('groupAssignmentId', $groupAssignmentId);
            $results = $q->getQuery()->execute();

            /** @var GroupAssignment $groupAssignment */
            $groupAssignment = $this->getRepository(GroupAssignment::class)->find($groupAssignmentId);
            $totalTasks = $groupAssignment->getTaskGroups()->count();
            $completedTasks = $groupAssignment->getTaskGroups()->filter(function (TaskGroup $taskGroup) use ($taskGroupId) {
                if ($taskGroup->getId() === $taskGroupId) {
                    $this->getEntityManager()->refresh($taskGroup);
                }
                return $taskGroup->isCompleted();
            })->count();

            if ($totalTasks === $completedTasks) {
                if (!$groupAssignment->getCompleted()) {
                    $this->getGroupAssignmentService()->changeCompleteState($groupAssignmentId, true);
                }
            } else {
                if ($groupAssignment->getCompleted()) {
                    $this->getGroupAssignmentService()->changeCompleteState($groupAssignmentId, false);
                }
            }

            return $results;
        } catch (Exception $e) {
            throw new DaoException($e->getTraceAsString());
        }
    }

    public function findTaskGroupById(int $id)
    {
        return $this->getRepository(TaskGroup::class)->find($id);
    }
}