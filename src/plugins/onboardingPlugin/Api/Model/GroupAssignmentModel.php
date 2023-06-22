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
            ['getSupervisor', 'getFullName'],
            ['getCreatedBy', 'getFullName'],
            ['getEmployee', 'getFullName'],
            ['getEmployee', 'getEmpNumber'],
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
            ['supervisor', 'name'],
            ['creator', 'name'],
            ['assignee', 'name'],
            ['assignee', 'id'],
        ]);
    }
}