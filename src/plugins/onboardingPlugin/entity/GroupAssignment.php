<?php

namespace OrangeHRM\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OrangeHRM\Entity\Decorator\DecoratorTrait;
use OrangeHRM\Entity\Decorator\GroupAssignmentDecorator;
use OrangeHRM\Onboarding\Traits\Service\TaskTypeServiceTrait;
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
    use SoftDeletes, DecoratorTrait, CreatedBy, TaskTypeServiceTrait;

    public function __construct()
    {
        $this->taskGroups = new ArrayCollection();
    }

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
     * @ORM\Column(name="types", type="string", nullable=true)
     */
    private ?string $types = null;

    /**
     * @ORM\Column(name="end_date", type="string", nullable=true)
     */
    private ?string $endDate = null;

    /**
     * @ORM\Column(name="due_date", type="string", nullable=true)
     */
    private ?string $dueDate = null;

    /**
     * @ORM\Column(name="submitted_at", type="string", nullable=true)
     */
    private ?string $submittedAt = null;

    /**
     * @ORM\Column(name="completed", type="boolean", options={"default" : 0})
     */
    private bool $completed = false;

    /**
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private ?string $name;

    /**
     * @ORM\Column(name="notes", type="string",  nullable=true)
     */
    private ?string $notes;

    /**
     * @ORM\Column(name="emp_number", type="integer", nullable=true)
     */
    private ?int $empNumber;

    /**
     * @ORM\Column(name="supervisor_number", type="integer", nullable=true)
     */
    private ?int $supervisorNumber;

    /**
     * @var Collection<int, TaskGroup>
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\TaskGroup", mappedBy="groupAssignment", cascade={"persist","remove"})
     */
    private Collection $taskGroups;

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

    /**
     * @return bool
     */
    public function getCompleted(): bool
    {
        return $this->completed;
    }

    /**
     * @param bool $completed
     */
    public function setCompleted(bool $completed): void
    {
        $this->completed = $completed;
    }

    /**
     * @return Collection
     */
    public function getTaskGroups(): Collection
    {
        return $this->taskGroups;
    }

    /**
     * @param Collection $taskGroups
     */
    public function setTaskGroups(Collection $taskGroups): void
    {
        $this->taskGroups = $taskGroups;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getNotes(): ?string
    {
        return $this->notes;
    }

    /**
     * @param string|null $notes
     */
    public function setNotes(?string $notes): void
    {
        $this->notes = $notes;
    }

    public function getTaskTypes(): array
    {
        if(!$this->getTypes()) {
            return [];
        }
        return $this->getTaskTypeService()->getTaskTypesById(explode(',', $this->getTypes()));
    }

    /**
     * @return string|null
     */
    public function getTypes(): ?string
    {
        return $this->types;
    }

    /**
     * @param string|null $types
     */
    public function setTypes(?string $types): void
    {
        $this->types = $types;
    }

    /**
     * @return string|null
     */
    public function getSubmittedAt(): ?string
    {
        return $this->submittedAt;
    }

    /**
     * @param string|null $submittedAt
     */
    public function setSubmittedAt(?string $submittedAt): void
    {
        $this->submittedAt = $submittedAt;
    }

    public function setEmpNumber(?int $empNumber): void
    {
        $this->empNumber = $empNumber;
    }

    /**
     * @param int|null $supervisorNumber
     */
    public function setSupervisorNumber(?int $supervisorNumber): void
    {
        $this->supervisorNumber = $supervisorNumber;
    }

    public function getProgress(): float{
        $completedTasks = $this->getTaskGroups()->filter(function (TaskGroup $taskGroup){
            return $taskGroup->isCompleted();
        })->count();

        if($completedTasks === 0) {
            return 0;
        }
        $totalTasks = $this->getTaskGroups()->count();

        return round($completedTasks / $totalTasks * 100, 2);
    }
}