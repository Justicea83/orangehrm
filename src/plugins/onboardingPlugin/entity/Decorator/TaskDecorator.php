<?php

namespace OrangeHRM\Entity\Decorator;

use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\Task;
use OrangeHRM\Entity\TaskType;

class TaskDecorator
{
    use EntityManagerHelperTrait;
    use DateTimeHelperTrait;

    protected Task $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * @return Task
     */
    public function getTask(): Task
    {
        return $this->task;
    }

    public function getCreatedAt(): ?string
    {
        $date = $this->getTask()->getCreatedAt();
        return $this->getDateTimeHelper()->formatDate($date);
    }

    public function setTaskTypeById(?int $id): void
    {
        if (!$id) {
            return;
        }
        /** @var TaskType|null $taskType */
        $taskType = $this->getReference(TaskType::class, $id);
        $this->getTask()->setTaskType($taskType);
    }

    public function getUpdatedAt(): ?string
    {
        $date = $this->getTask()->getUpdatedAt();
        return $this->getDateTimeHelper()->formatDate($date);
    }
}