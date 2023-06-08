<?php

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;
use OrangeHRM\Entity\Decorator\DecoratorTrait;
use OrangeHRM\Entity\Decorator\GroupAssignmentDecorator;
use OrangeHRM\ORM\Utils\CreatedBy;
use OrangeHRM\ORM\Utils\SoftDeletes;
use OrangeHRM\ORM\Utils\TenantAwareWithTimeStamps;

/**
 * @method GroupAssignmentDecorator getDecorator()
 *
 * @ORM\Table(name="group_assignments")
 * @ORM\Entity
 */
class GroupAssignment extends TenantAwareWithTimeStamps
{
    use SoftDeletes, DecoratorTrait, CreatedBy;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(name="start_date", type="string", nullable=true)
     */
    private ?string $startDate = null;

    /**
     * @ORM\Column(name="end_date", type="string", nullable=true)
     */
    private ?string $endDate = null;

    /**
     * @ORM\Column(name="due_date", type="string", nullable=true)
     */
    private ?string $dueDate = null;

    /**
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\TaskAssignment", inversedBy="groupAssignments", cascade={"persist","remove"})
     * @ORM\JoinColumn(name="task_assignment_id", referencedColumnName="id", nullable=true)
     */
    private ?TaskAssignment $taskAssignment = null;

    /**
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Employee", inversedBy="group_assignments")
     * @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number", nullable=true)
     */
    private ?Employee $employee = null;

    /**
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Employee", inversedBy="group_assignments")
     * @ORM\JoinColumn(name="supervisor_number", referencedColumnName="emp_number", nullable=true)
     */
    private ?Employee $supervisor = null;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getStartDate(): ?string
    {
        return $this->startDate;
    }

    /**
     * @param string|null $startDate
     */
    public function setStartDate(?string $startDate): void
    {
        $this->startDate = $startDate;
    }

    /**
     * @return string|null
     */
    public function getEndDate(): ?string
    {
        return $this->endDate;
    }

    /**
     * @param string|null $endDate
     */
    public function setEndDate(?string $endDate): void
    {
        $this->endDate = $endDate;
    }

    /**
     * @return string|null
     */
    public function getDueDate(): ?string
    {
        return $this->dueDate;
    }

    /**
     * @param string|null $dueDate
     */
    public function setDueDate(?string $dueDate): void
    {
        $this->dueDate = $dueDate;
    }

    /**
     * @return TaskAssignment|null
     */
    public function getTaskAssignment(): ?TaskAssignment
    {
        return $this->taskAssignment;
    }

    /**
     * @param TaskAssignment|null $taskAssignment
     */
    public function setTaskAssignment(?TaskAssignment $taskAssignment): void
    {
        $this->taskAssignment = $taskAssignment;
    }

    /**
     * @return Employee|null
     */
    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    /**
     * @param Employee|null $employee
     */
    public function setEmployee(?Employee $employee): void
    {
        $this->employee = $employee;
    }

    /**
     * @return Employee|null
     */
    public function getSupervisor(): ?Employee
    {
        return $this->supervisor;
    }

    /**
     * @param Employee|null $supervisor
     */
    public function setSupervisor(?Employee $supervisor): void
    {
        $this->supervisor = $supervisor;
    }
}