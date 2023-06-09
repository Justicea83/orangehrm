<?php

namespace OrangeHRM\Onboarding\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\ModelTrait;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Entity\GroupAssignment;

class GroupAssignmentDetailModel implements Normalizable
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
            [
                'getTaskGroups',
                [
                    'getId',
                    'isCompleted',
                    'getDueDate',
                    'getPriority',
                    ['getTask', 'getId', 'getTitle', 'getNotes']
                ]
            ],
        ]);

        $this->setAttributeNames([
            'id',
            'notes',
            'name',
            'startDate',
            'endDate',
            'completed',
            'dueDate',
            [
                'taskGroups',
                [
                    'id',
                    'isCompleted',
                    'dueDate',
                    'priority',
                    ['task', 'id', 'title', 'notes'],
                ]
            ],
        ]);
    }
}