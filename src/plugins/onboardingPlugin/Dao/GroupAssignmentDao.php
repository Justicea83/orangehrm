<?php

namespace OrangeHRM\Onboarding\Dao;

use Carbon\Carbon;
use Exception;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Entity\GroupAssignment;
use OrangeHRM\Entity\TaskGroup;
use OrangeHRM\Onboarding\Dto\GroupAssignmentSearchFilterParams;
use OrangeHRM\Onboarding\Exception\PermissionDeniedException;
use OrangeHRM\ORM\ListSorter;
use OrangeHRM\ORM\QueryBuilderWrapper;

class GroupAssignmentDao extends BaseDao
{
    use AuthUserTrait;

    public function saveTaskAssignment(GroupAssignment $groupAssignment): GroupAssignment
    {
        $this->persist($groupAssignment);
        return $groupAssignment;
    }

    // My group assignments
    public function getMyGroupAssignments(GroupAssignmentSearchFilterParams $filterParams): array
    {
        $qb = $this->getGroupAssignmentListQueryBuilderWrapper($filterParams)->getQueryBuilder();
        $qb->andWhere('g.empNumber = :employeeId')
            ->setParameter('employeeId', $this->getAuthUser()->getEmpNumber());
        $qb->andWhere('g.status = :status')
            ->setParameter('status', GroupAssignment::STATUS_PENDING);
        return $qb->getQuery()->execute();
    }

    public function getMyGroupAssignmentsCount(GroupAssignmentSearchFilterParams $filterParams): int
    {
        $qb = $this->getGroupAssignmentListQueryBuilderWrapper($filterParams)->getQueryBuilder();
        $qb->andWhere('g.empNumber = :employeeId')
            ->setParameter('employeeId', $this->getAuthUser()->getEmpNumber());
        return $this->getPaginator($qb)->count();
    }

    // Employee Group Assignments
    public function getEmployeeAssignments(GroupAssignmentSearchFilterParams $filterParams): array
    {
        $qb = $this->getGroupAssignmentListQueryBuilderWrapper($filterParams)->getQueryBuilder();
        $qb->andWhere('g.supervisorNumber = :employeeId')
            ->setParameter('employeeId', $this->getAuthUser()->getEmpNumber());
        return $qb->getQuery()->execute();
    }

    public function getEmployeeAssignmentsCount(GroupAssignmentSearchFilterParams $filterParams): int
    {
        $qb = $this->getGroupAssignmentListQueryBuilderWrapper($filterParams)->getQueryBuilder();
        $qb->andWhere('g.supervisorNumber = :employeeId')
            ->setParameter('employeeId', $this->getAuthUser()->getEmpNumber());
        return $this->getPaginator($qb)->count();
    }

    // All Group Assignments
    public function getGroupAssignments(GroupAssignmentSearchFilterParams $filterParams): array
    {
        $qb = $this->getGroupAssignmentListQueryBuilderWrapper($filterParams)->getQueryBuilder();
        $qb->andWhere('g.creatorId = :employeeId')
            ->setParameter('employeeId', $this->getAuthUser()->getEmpNumber());
        return $qb->getQuery()->execute();
    }

    public function getGroupAssignmentsCount(GroupAssignmentSearchFilterParams $filterParams): int
    {
        $qb = $this->getGroupAssignmentListQueryBuilderWrapper($filterParams)->getQueryBuilder();
        $qb->andWhere('g.creatorId = :employeeId')
            ->setParameter('employeeId', $this->getAuthUser()->getEmpNumber());
        return $this->getPaginator($qb)->count();
    }

    protected function getGroupAssignmentListQueryBuilderWrapper(GroupAssignmentSearchFilterParams $filterParams): QueryBuilderWrapper
    {
        $q = $this->createQueryBuilder(GroupAssignment::class, 'g');
        $q->distinct();

        $this->setSortingAndPaginationParams($q, $filterParams);

        if (!is_null($filterParams->getName())) {
            $q->andWhere(
                $q->expr()->like('g.name', ':name'),
            );
            $q->setParameter('name', '%' . $filterParams->getName() . '%');
        }

        if (!is_null($filterParams->getSubmittedAt())) {
            $q->andWhere(
                $q->expr()->isNotNull('g.submittedAt')
            )->andWhere(
                $q->expr()->gte('g.submittedAt', ':submittedAt')
            );
            $q->setParameter('submittedAt', $filterParams->getSubmittedAt());
        }

        if (!is_null($filterParams->getStartDate())) {
            $q->andWhere(
                $q->expr()->gte('g.startDate', ':startDate')
            );
            $q->setParameter('startDate', $filterParams->getStartDate());
        }

        if (!is_null($filterParams->getEndDate())) {
            $q->andWhere(
                $q->expr()->lte('g.endDate', ':endDate')
            );
            $q->setParameter('endDate', $filterParams->getEndDate());
        }

        if (!is_null($filterParams->getDueDate())) {
            $q->andWhere(
                $q->expr()->gte('g.dueDate', ':dueDate')
            );
            $q->setParameter('dueDate', $filterParams->getDueDate());
        }

        if (!is_null($filterParams->getEmployeeNumber())) {
            $q->andWhere(
                $q->expr()->eq('g.empNumber', ':empNumber')
            );
            $q->setParameter('empNumber', $filterParams->getEmployeeNumber());
        }

        if (!is_null($filterParams->getSupervisorNumber())) {
            $q->andWhere(
                $q->expr()->eq('g.supervisorNumber', ':supervisorNumber')
            );
            $q->setParameter('supervisorNumber', $filterParams->getSupervisorNumber());
        }

        if (!is_null($filterParams->isCompleted())) {
            $q->andWhere(
                $q->expr()->eq('g.completed', ':completed')
            );
            $q->setParameter('completed', $filterParams->isCompleted());
        }

        $q->orderBy('g.id', ListSorter::DESCENDING);

        return $this->getQueryBuilderWrapper($q);
    }

    /**
     * @throws DaoException
     */
    private function isOwner(int $id): bool
    {
        $groupAssignment = $this->getGroupAssignmentById($id);
        return $groupAssignment->getCreatorId() === $this->getAuthUser()->getEmpNumber();
    }

    /**
     * @throws DaoException
     */
    public function getGroupAssignmentById(int $id): ?GroupAssignment
    {
        try {
            $groupAssignment = $this->getRepository(GroupAssignment::class)->find($id);
            if ($groupAssignment instanceof GroupAssignment) {
                return $groupAssignment;
            }
            return null;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @throws DaoException
     * @throws PermissionDeniedException
     */
    public function deleteGroupAssignmentById(array $ids): int
    {
        $q = $this->createQueryBuilder(GroupAssignment::class, 'g');
        $q->distinct();
        $q->andWhere(
            $q->expr()->in('g.id', ':ids')
        )->setParameter('ids', $ids);
        $q->andWhere('g.creatorId = :creatorId')
            ->setParameter('creatorId', $this->getAuthUser()->getEmpNumber());

        $results = $q->getQuery()->execute();

        $idsToDelete = array_map(fn(GroupAssignment $groupAssignment) => $groupAssignment->getId(), $results);

        if (count($idsToDelete) === 0) {
            throw new PermissionDeniedException();
        }

        try {
            $q = $this->createQueryBuilder(GroupAssignment::class, 'g');
            $q->delete()
                ->where($q->expr()->in('g.id', ':ids'))
                ->setParameter('ids', $idsToDelete);
            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @throws DaoException
     * @throws PermissionDeniedException
     */
    public function markAsComplete(int $id): void
    {
        if (!$this->isOwner($id)) {
            throw new PermissionDeniedException();
        }
        $this->beginTransaction();
        try {
            $q = $this->createQueryBuilder(TaskGroup::class, 't');
            $q->update()
                ->set('t.completed', true)
                ->andWhere($q->expr()->eq('t.groupAssignmentId', ':groupAssignmentId'))
                ->setParameter('groupAssignmentId', $id);
            $q->getQuery()->execute();

            $q = $this->createQueryBuilder(GroupAssignment::class, 'g');
            $q->update()
                ->set('g.completed', true)
                ->andWhere($q->expr()->eq('g.id', ':id'))
                ->setParameter('id', $id);
            $q->getQuery()->execute();

            $this->commitTransaction();
        } catch (Exception $e) {
            $this->rollBackTransaction();
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @throws DaoException
     */
    public function submit(int $id): void
    {
        try {
            $q = $this->createQueryBuilder(GroupAssignment::class, 'g');
            $q->update()
                ->set('g.submittedAt', ':date')
                ->setParameter('date', Carbon::now()->toDateTimeString())
                ->andWhere($q->expr()->eq('g.id', ':id'))
                ->setParameter('id', $id);
            $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    public function changeCompleteState(int $id, bool $state): void
    {
        $q = $this->createQueryBuilder(GroupAssignment::class, 'g');
        $q->update()
            ->set('g.completed', ':state')
            ->setParameter('state', $state)
            ->andWhere($q->expr()->eq('g.id', ':id'))
            ->setParameter('id', $id);
        $q->getQuery()->execute();
    }

    private function changeState(int $id, string $state): void
    {
        $q = $this->createQueryBuilder(GroupAssignment::class, 'g');
        $q->update()
            ->set('g.status', ':state')
            ->setParameter('state', $state)
            ->andWhere($q->expr()->eq('g.id', ':id'))
            ->setParameter('id', $id);
        $q->getQuery()->execute();
    }

    /**
     * @throws DaoException
     * @throws PermissionDeniedException
     */
    public function approveAssignment(int $id)
    {
        if (!$this->isOwner($id)) {
            throw new PermissionDeniedException();
        }
        $this->changeState($id, GroupAssignment::STATUS_APPROVED);
    }

    /**
     * @throws DaoException
     * @throws PermissionDeniedException
     */
    public function rejectAssignment(int $id)
    {
        if (!$this->isOwner($id)) {
            throw new PermissionDeniedException();
        }
        $this->changeState($id, GroupAssignment::STATUS_REJECTED);
    }
}