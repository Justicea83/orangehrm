<?php

namespace OrangeHRM\Onboarding\Dao;

use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\TaskAssignment;

class TaskAssignmentDao extends BaseDao
{
    public function saveTaskAssignment(TaskAssignment $taskAssignment): TaskAssignment
    {
        $this->persist($taskAssignment);
        return $taskAssignment;
    }
}