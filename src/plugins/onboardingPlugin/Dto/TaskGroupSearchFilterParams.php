<?php

namespace OrangeHRM\Onboarding\Dto;

use OrangeHRM\Core\Dto\FilterParams;

class TaskGroupSearchFilterParams extends FilterParams
{
    public const ALLOWED_SORT_FIELDS = [
        't.priority',
        't.dueDate',
        't.completed',
        't.groupAssignmentId'
    ];

    private ?int $priority = null;
    private ?int $groupAssignmentId = null;
    private ?string $dueDate = null;
    private ?bool $completed = null;

    public static function instance(): TaskGroupSearchFilterParams
    {
        return new TaskGroupSearchFilterParams();
    }

    /**
     * @param bool|null $completed
     * @return TaskGroupSearchFilterParams
     */
    public function setCompleted(?bool $completed): TaskGroupSearchFilterParams
    {
        $this->completed = $completed;
        return $this;
    }

    /**
     * @param string|null $dueDate
     * @return TaskGroupSearchFilterParams
     */
    public function setDueDate(?string $dueDate): TaskGroupSearchFilterParams
    {
        $this->dueDate = $dueDate;
        return $this;
    }

    /**
     * @param int|null $priority
     * @return TaskGroupSearchFilterParams
     */
    public function setPriority(?int $priority): TaskGroupSearchFilterParams
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getCompleted(): ?bool
    {
        return $this->completed;
    }

    /**
     * @return string|null
     */
    public function getDueDate(): ?string
    {
        return $this->dueDate;
    }

    /**
     * @return int|null
     */
    public function getPriority(): ?int
    {
        return $this->priority;
    }

    /**
     * @param int|null $groupAssignmentId
     * @return TaskGroupSearchFilterParams
     */
    public function setGroupAssignmentId(?int $groupAssignmentId): TaskGroupSearchFilterParams
    {
        $this->groupAssignmentId = $groupAssignmentId;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getGroupAssignmentId(): ?int
    {
        return $this->groupAssignmentId;
    }
}