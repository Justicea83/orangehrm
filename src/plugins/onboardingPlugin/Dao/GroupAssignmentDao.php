<?php

namespace OrangeHRM\Onboarding\Dao;

use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\GroupAssignment;

class GroupAssignmentDao extends BaseDao
{
    public function saveTaskAssignment(GroupAssignment $groupAssignment): GroupAssignment
    {
        $this->persist($groupAssignment);
        return $groupAssignment;
    }
}