<?php

namespace OrangeHRM\Onboarding\Service;

use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\Task;
use OrangeHRM\Entity\TaskType;
use OrangeHRM\Onboarding\Dao\TaskTypeDao;

class TaskTypeService
{
    protected ?TaskTypeDao $taskTypeDao = null;

    public function getTaskTypeDao(): ?TaskTypeDao
    {
        if (is_null($this->taskTypeDao)) {
            $this->taskTypeDao = new TaskTypeDao();
        }
        return $this->taskTypeDao;
    }

    /**
     * @throws DaoException
     */
    public function getTaskTypeById(int $id): ?TaskType
    {
        return $this->getTaskTypeDao()->getTaskTypeById($id);
    }

    public function saveTaskType(TaskType $Task): TaskType
    {
        return $this->getTaskTypeDao()->saveTaskType($Task);
    }

    public function getTaskTypeList(): array
    {
        return $this->getTaskTypeDao()->getTaskTypeList();
    }

    /**
     * @throws DaoException
     */
    public function deleteTaskType(array $ids): void
    {
        $this->getTaskTypeDao()->deleteTaskTypeById($ids);
    }

    public function getTaskTypesById(array $ids): array
    {
       return  $this->getTaskTypeDao()->getTaskTypesById($ids);
    }
}