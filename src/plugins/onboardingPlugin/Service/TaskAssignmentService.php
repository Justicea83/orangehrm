<?php

namespace OrangeHRM\Onboarding\Service;

use OrangeHRM\Entity\TaskAssignment;
use OrangeHRM\Onboarding\Dao\TaskAssignmentDao;

class TaskAssignmentService
{
    protected ?TaskAssignmentDao $taskAssignmentDao = null;

    public function getTaskAssignmentDao(): TaskAssignmentDao
    {
        if (is_null($this->taskAssignmentDao)) {
            $this->taskAssignmentDao = new TaskAssignmentDao();
        }
        return $this->taskAssignmentDao;
    }

    /**
     * @param TaskAssignmentDao|null $taskAssignmentDao
     */
    public function setTaskAssignmentDao(?TaskAssignmentDao $taskAssignmentDao): void
    {
        $this->taskAssignmentDao = $taskAssignmentDao;
    }

    public function saveTaskAssignment(TaskAssignment $taskAssignment): TaskAssignment
    {
        return $this->getTaskAssignmentDao()->saveTaskAssignment($taskAssignment);
    }
}