<?php

namespace OrangeHRM\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OrangeHRM\ORM\Utils\SoftDeletes;
use OrangeHRM\ORM\Utils\TenantAwareWithTimeStamps;

/**
 *
 * @ORM\Table(name="task_assignments")
 * @ORM\Entity
 */
class TaskAssignment extends TenantAwareWithTimeStamps
{
    use SoftDeletes;

    public function __construct() {
        $this->taskGroups = new ArrayCollection();
        $this->groupAssignments = new ArrayCollection();
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
     * @ORM\Column(name="notes", type="string")
     */
    private ?string $notes;

    /**
     * @ORM\Column(name="name", type="string", length=255)
     */
    private ?string $name;

    /**
     * @ORM\Column(name="type", type="integer")
     */
    private int $type;

    /**
     * @var Collection<int, TaskGroup>
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\TaskGroup", mappedBy="taskAssignment", cascade={"persist","remove"})
     */
    private Collection $taskGroups;

    /**
     * @var Collection<int, GroupAssignment>
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\GroupAssignment", mappedBy="taskAssignment", cascade={"persist","remove"})
     */
    private Collection $groupAssignments;

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

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param int $type
     */
    public function setType(int $type): void
    {
        $this->type = $type;
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
     * @return Collection
     */
    public function getGroupAssignments(): Collection
    {
        return $this->groupAssignments;
    }

    /**
     * @param Collection $groupAssignments
     */
    public function setGroupAssignments(Collection $groupAssignments): void
    {
        $this->groupAssignments = $groupAssignments;
    }
}