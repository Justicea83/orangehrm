<?php

namespace OrangeHRM\Onboarding\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\ModelTrait;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Entity\GroupAssignment;

class GroupAssignmentModel implements Normalizable
{
    use ModelTrait;

    public function __construct(GroupAssignment $groupAssignment)
    {
        $this->setEntity($groupAssignment);

        $this->setFilters([
            'id',
            'notes',
            'name',
            'startDate',
            'endDate',
            'completed',
            'dueDate',
            'submittedAt',
            'getProgress',
            'getStatus',
            ['getSupervisor', 'getFullName'],
            ['getSupervisor', 'getEmpNumber'],
            ['getCreatedBy', 'getFullName'],
            ['getCreatedBy', 'getEmpNumber'],
            ['getEmployee', 'getFullName'],
            ['getEmployee', 'getEmpNumber'],
            'getComments',
        ]);

        $this->setAttributeNames([
            'id',
            'notes',
            'name',
            'startDate',
            'endDate',
            'completed',
            'dueDate',
            'submittedAt',
            'progress',
            'status',
            ['supervisor', 'name'],
            ['supervisor', 'id'],
            ['creator', 'name'],
            ['creator', 'id'],
            ['assignee', 'name'],
            ['assignee', 'id'],
            'comments'
        ]);
    }
}