<?php

namespace OrangeHRM\Onboarding\Dto;

use OrangeHRM\Core\Dto\FilterParams;

class GroupAssignmentSearchFilterParams extends FilterParams
{
    public const ALLOWED_SORT_FIELDS = [
        'name',
        'startDate',
        'endDate',
        'dueDate',
        'submittedAt',
        'completed',
        'empNumber',
        'supervisorNumber',
    ];

    private ?string $startDate = null;
    private ?string $endDate = null;
    private ?string $dueDate = null;
    private ?bool $completed = null;
    private ?string $name = null;
    private ?int $employeeNumber = null;
    private ?string $submittedAt = null;
    private ?int $supervisorNumber = null;
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

    public function getSupervisorNumber(): ?int
    {
        return $this->supervisorNumber;
    }

    public function getEmployeeNumber(): ?int
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

    public function isCompleted(): ?bool
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

    /**
     * @param string|null $submittedAt
     * @return GroupAssignmentSearchFilterParams
     */
    public function setSubmittedAt(?string $submittedAt): GroupAssignmentSearchFilterParams
    {
        $this->submittedAt = $submittedAt;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSubmittedAt(): ?string
    {
        return $this->submittedAt;
    }
}