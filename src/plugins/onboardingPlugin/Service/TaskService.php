<?php

namespace OrangeHRM\Onboarding\Service;

use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\Task;
use OrangeHRM\Onboarding\Dao\TaskDao;
use OrangeHRM\Onboarding\Dto\TaskSearchFilterParams;

class TaskService
{
    protected ?TaskDao $taskDao = null;

    public function getTaskDao(): TaskDao
    {
        if (is_null($this->taskDao)) {
            $this->taskDao = new TaskDao();
        }
        return $this->taskDao;
    }

    public function getTaskList(TaskSearchFilterParams $taskSearchFilterParams): array
    {
        return $this->getTaskDao()->getTaskList($taskSearchFilterParams);
    }

    public function getTaskListCount(TaskSearchFilterParams $taskSearchFilterParams): int
    {
        return $this->getTaskDao()->getTaskListCount($taskSearchFilterParams);
    }

    public function saveTask(Task $Task): Task
    {
        return $this->getTaskDao()->saveTask($Task);
    }

    public function getUnDeletableTaskIds(): array
    {
        return [];
    }

    /**
     * @throws DaoException
     */
    public function getTaskById(int $id): ?Task
    {
        return $this->getTaskDao()->getTaskById($id);
    }

    /**
     * @throws DaoException
     */
    public function deleteTask(array $ids): int
    {
        return $this->getTaskDao()->deleteTaskById($ids);
    }
}