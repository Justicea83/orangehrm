<?php

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;
use OrangeHRM\Entity\Decorator\DecoratorTrait;
use OrangeHRM\Entity\Decorator\TaskGroupDecorator;

/**
 * @method TaskGroupDecorator getDecorator()
 *
 * @ORM\Table(name="task_groups")
 * @ORM\Entity
 */
class TaskGroup
{
    use DecoratorTrait;
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(name="due_date", type="string", nullable=true)
     */
    private ?string $dueDate = null;

    /**
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Task")
     * @ORM\JoinColumn(name="task_id", referencedColumnName="id", nullable=true)
     */
    private ?Task $task = null;

    /**
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\GroupAssignment", inversedBy="taskGroups", cascade={"persist","remove"})
     * @ORM\JoinColumn(name="group_assignment_id", referencedColumnName="id", nullable=true)
     */
    private ?GroupAssignment $groupAssignment = null;

    /**
     * @ORM\Column(name="group_assignment_id", type="integer")
     */
    private ?int $groupAssignmentId = null;

    /**
     * @ORM\Column(name="completed", type="boolean", options={"default" : 0})
     */
    private bool $completed = false;

    /**
     * @ORM\Column(name="priority", type="integer")
     */
    private int $priority;

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
     * @return Task|null
     */
    public function getTask(): ?Task
    {
        return $this->task;
    }

    /**
     * @param Task|null $task
     */
    public function setTask(?Task $task): void
    {
        $this->task = $task;
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     */
    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }

    /**
     * @return bool
     */
    public function isCompleted(): bool
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
     * @return GroupAssignment|null
     */
    public function getGroupAssignment(): ?GroupAssignment
    {
        return $this->groupAssignment;
    }

    /**
     * @param GroupAssignment|null $groupAssignment
     */
    public function setGroupAssignment(?GroupAssignment $groupAssignment): void
    {
        $this->groupAssignment = $groupAssignment;
    }
}