<?php

namespace OrangeHRM\Entity\Decorator;

use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\GroupAssignment;
use OrangeHRM\Entity\TaskAssignment;

class GroupAssignmentDecorator
{
    use EntityManagerHelperTrait;

    private GroupAssignment $groupAssignment;

    public function __construct(GroupAssignment $groupAssignment)
    {
        $this->groupAssignment = $groupAssignment;
    }

    public function setTaskAssignmentById(?int $id): void
    {
        if (!$id) {
            return;
        }
        /** @var TaskAssignment|null $taskAssignment */
        $taskAssignment = $this->getReference(TaskAssignment::class, $id);
        $this->getGroupAssignment()->setTaskAssignment($taskAssignment);
    }

    public function setEmployeeById(?int $id): void
    {
        if (!$id) {
            return;
        }
        /** @var Employee|null $employee */
        $employee = $this->getReference(Employee::class, $id);

        $this->getGroupAssignment()->setEmployee($employee);
    }

    public function setSupervisorById(?int $id): void
    {
        if (!$id) {
            return;
        }
        /** @var Employee|null $employee */
        $employee = $this->getReference(Employee::class, $id);
        $this->getGroupAssignment()->setSupervisor($employee);
    }

    /**
     * @return GroupAssignment
     */
    public function getGroupAssignment(): GroupAssignment
    {
        return $this->groupAssignment;
    }
}