<?php

namespace OrangeHRM\Onboarding\Dao;

use Carbon\Carbon;
use Exception;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\Task;
use OrangeHRM\Entity\TaskType;

class TaskTypeDao extends BaseDao
{
    public function getTaskTypeList(): array
    {
        $q = $this->createQueryBuilder(TaskType::class, 't');
        $q->andWhere(
            $q->expr()->isNull('t.deletedAt')
        );
        return $q->getQuery()->execute();
    }

    public function saveTaskType(TaskType $taskType): TaskType
    {
        $this->persist($taskType);
        return $taskType;
    }

    public function getTaskTypesById(array $ids): array
    {
        $q = $this->createQueryBuilder(TaskType::class, 't');
        $q->andWhere(
            $q->expr()->isNull('t.deletedAt')
        );
        $q->andWhere(
            $q->expr()->in('t.id', ':ids')
        )->setParameter('ids', $ids);

        return $q->getQuery()->execute();
    }

    /**
     * @throws DaoException
     */
    public function getTaskTypeById(int $id): ?TaskType
    {
        try {
            $taskType = $this->getRepository(TaskType::class)->find($id);
            if ($taskType instanceof TaskType) {
                return $taskType;
            }
            return null;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @throws DaoException
     */
    public function deleteTaskTypeById(array $ids)
    {
        try {
            $q = $this->createQueryBuilder(TaskType::class, 't');
            $q->update()
                ->set('t.deletedAt', ':deletedAt')
                ->setParameter('deletedAt', Carbon::now()->toDateString())
                ->where($q->expr()->in('t.id', ':ids'))
                ->setParameter('ids', $ids);
            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }
}