<?php

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;
use OrangeHRM\Entity\Decorator\DecoratorTrait;
use OrangeHRM\Entity\Decorator\TaskDecorator;
use OrangeHRM\ORM\Utils\TenantAwareWithTimeStamps;

/**
 * @method TaskDecorator getDecorator()
 *
 * @ORM\Table(name="tasks")
 * @ORM\Entity
 */
class Task extends TenantAwareWithTimeStamps
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
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private string $title;

    /**
     * @var string|null
     *
     * @ORM\Column(name="notes", type="string", nullable=true)
     */
    private ?string $notes;

    /**
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\TaskType", inversedBy="tasks")
     * @ORM\JoinColumn(name="type", referencedColumnName="id", nullable=true)
     */
    private ?TaskType $taskType = null;

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
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): void
    {
        $this->notes = $notes;
    }



    /**
     * @return TaskType|null
     */
    public function getTaskType(): ?TaskType
    {
        return $this->taskType;
    }

    /**
     * @param TaskType|null $taskType
     */
    public function setTaskType(?TaskType $taskType): void
    {
        $this->taskType = $taskType;
    }
}