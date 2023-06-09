<?php

namespace OrangeHRM\Onboarding\Service;

use OrangeHRM\Entity\GroupAssignment;
use OrangeHRM\Onboarding\Dao\GroupAssignmentDao;
use OrangeHRM\Onboarding\Dto\GroupAssignmentSearchFilterParams;

class GroupAssignmentService
{
    protected ?GroupAssignmentDao $groupAssignmentDao = null;

    public function getGroupAssignmentDao(): GroupAssignmentDao
    {
        if (is_null($this->groupAssignmentDao)) {
            $this->groupAssignmentDao = new GroupAssignmentDao();
        }
        return $this->groupAssignmentDao;
    }

    public function saveGroupAssignment(GroupAssignment $taskAssignment): GroupAssignment
    {
        return $this->getGroupAssignmentDao()->saveTaskAssignment($taskAssignment);
    }

    public function getMyGroupAssignments(GroupAssignmentSearchFilterParams $filterParams): array
    {
        return $this->getGroupAssignmentDao()->getMyGroupAssignments($filterParams);
    }

    public function getMyGroupAssignmentsCount(GroupAssignmentSearchFilterParams $filterParams): int
    {
        return $this->getGroupAssignmentDao()->getMyGroupAssignmentsCount($filterParams);
    }

    public function getEmployeeAssignments(GroupAssignmentSearchFilterParams $filterParams): array
    {
        return $this->getGroupAssignmentDao()->getEmployeeAssignments($filterParams);
    }

    public function getEmployeeAssignmentsCount(GroupAssignmentSearchFilterParams $filterParams): int
    {
        return $this->getGroupAssignmentDao()->getEmployeeAssignmentsCount($filterParams);
    }

    public function getGroupAssignments(GroupAssignmentSearchFilterParams $filterParams): array
    {
        return $this->getGroupAssignmentDao()->getGroupAssignments($filterParams);
    }

    public function getGroupAssignmentsCount(GroupAssignmentSearchFilterParams $filterParams): int
    {
        return $this->getGroupAssignmentDao()->getGroupAssignmentsCount($filterParams);
    }
}