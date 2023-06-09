<?php

namespace OrangeHRM\Onboarding\Service;

use OrangeHRM\Entity\GroupAssignment;
use OrangeHRM\Onboarding\Dao\GroupAssignmentDao;

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
}