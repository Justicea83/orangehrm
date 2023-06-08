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
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Task", inversedBy="task_groups", cascade={"persist","remove"})
     * @ORM\JoinColumn(name="task_id", referencedColumnName="id", nullable=true)
     */
    private ?Task $task = null;

    /**
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\TaskAssignment", inversedBy="taskGroups", cascade={"persist","remove"})
     * @ORM\JoinColumn(name="task_assignment_id", referencedColumnName="id", nullable=true)
     */
    private ?TaskAssignment $taskAssignment = null;

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
}