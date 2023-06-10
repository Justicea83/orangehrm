<?php

namespace OrangeHRM\ORM\Utils;

use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\Employee;

trait CreatedBy
{
    use EntityManagerHelperTrait;

    /**
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Employee")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="emp_number", nullable=true)
     */
    private ?Employee $createdBy = null;

    /**
     * @ORM\Column(name="created_by", type="integer", nullable=true)
     */
    private ?int $creatorId = null;

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
        /** @var Employee|null $employee */
        $employee = $this->getReference(Employee::class, $id);
        $this->setCreatedBy($employee);
    }

    /**
     * @param Employee|null $createdBy
     */
    public function setCreatedBy(?Employee $createdBy): void
    {
        $this->createdBy = $createdBy;
    }
}