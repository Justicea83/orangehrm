<?php

namespace OrangeHRM\Onboarding\Dto;

use OrangeHRM\Core\Dto\FilterParams;

class GroupAssignmentSearchFilterParams extends FilterParams
{
    public const ALLOWED_SORT_FIELDS = [
        'g.name',
        'g.start_date',
        'g.end_date',
        'g.end_date',
        'g.due_date',
        'g.created_by',
        'g.completed',
        'g.emp_number',
        'g.supervisor_number',
    ];

    private ?string $startDate = null;
    private ?string $endDate = null;
    private ?string $dueDate = null;
    private ?bool $completed = null;
    private ?string $name = null;
    private ?string $employeeNumber = null;
    private ?string $supervisorNumber = null;
    private ?int $createdBy = null;

    public static function instance(): GroupAssignmentSearchFilterParams
    {
        return new GroupAssignmentSearchFilterParams();
    }

    /**
     * @param string|null $startDate
     * @return GroupAssignmentSearchFilterParams
     */
    public function setStartDate(?string $startDate): GroupAssignmentSearchFilterParams
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @param string|null $endDate
     * @return GroupAssignmentSearchFilterParams
     */
    public function setEndDate(?string $endDate): GroupAssignmentSearchFilterParams
    {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * @param string|null $dueDate
     * @return GroupAssignmentSearchFilterParams
     */
    public function setDueDate(?string $dueDate): GroupAssignmentSearchFilterParams
    {
        $this->dueDate = $dueDate;
        return $this;
    }

    public function setCompleted(?bool $completed): GroupAssignmentSearchFilterParams
    {
        $this->completed = $completed;
        return $this;
    }

    /**
     * @param string|null $name
     * @return GroupAssignmentSearchFilterParams
     */
    public function setName(?string $name): GroupAssignmentSearchFilterParams
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string|null $employeeNumber
     * @return GroupAssignmentSearchFilterParams
     */
    public function setEmployeeNumber(?string $employeeNumber): GroupAssignmentSearchFilterParams
    {
        $this->employeeNumber = $employeeNumber;
        return $this;
    }

    /**
     * @param string|null $supervisorNumber
     * @return GroupAssignmentSearchFilterParams
     */
    public function setSupervisorNumber(?string $supervisorNumber): GroupAssignmentSearchFilterParams
    {
        $this->supervisorNumber = $supervisorNumber;
        return $this;
    }

    /**
     * @param int|null $createdBy
     * @return GroupAssignmentSearchFilterParams
     */
    public function setCreatedBy(?int $createdBy): GroupAssignmentSearchFilterParams
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getCreatedBy(): ?int
    {
        return $this->createdBy;
    }

    /**
     * @return string|null
     */
    public function getSupervisorNumber(): ?string
    {
        return $this->supervisorNumber;
    }

    /**
     * @return string|null
     */
    public function getEmployeeNumber(): ?string
    {
        return $this->employeeNumber;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isCompleted(): bool
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
     * @return string|null
     */
    public function getEndDate(): ?string
    {
        return $this->endDate;
    }

    /**
     * @return string|null
     */
    public function getStartDate(): ?string
    {
        return $this->startDate;
    }
}