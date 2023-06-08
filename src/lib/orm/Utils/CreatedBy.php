<?php

namespace OrangeHRM\ORM\Utils;

use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\TaskAssignment;

trait CreatedBy
{
    use EntityManagerHelperTrait;

    /**
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Employee")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="emp_number", nullable=true)
     */
    private ?Employee $createdBy = null;

    /**
     * @return Employee|null
     */
    public function getCreatedBy(): ?Employee
    {
        return $this->createdBy;
    }

    public function setCreatorById(?int $id): void
    {
        if (!$id) {
            return;
        }
        /** @var TaskAssignment|null $taskAssignment */
        $taskAssignment = $this->getReference(Employee::class, $id);
        $this->setCreatedBy($taskAssignment);
    }

    /**
     * @param Employee|null $createdBy
     */
    public function setCreatedBy(?Employee $createdBy): void
    {
        $this->createdBy = $createdBy;
    }
}