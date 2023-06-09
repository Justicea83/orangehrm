<?php

namespace OrangeHRM\Onboarding\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\ModelTrait;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Entity\TaskGroup;

class TaskGroupDetailModel implements Normalizable
{
    use ModelTrait;

    public function __construct(TaskGroup $taskGroup)
    {
        $this->setEntity($taskGroup);

        $this->setFilters([
            [
                'getId',
                'isCompleted',
                'getDueDate',
                'getPriority',
                ['getTask', 'getId', 'getTitle', 'getNotes']
            ]
        ]);

        $this->setAttributeNames([
            [
                'id',
                'isCompleted',
                'dueDate',
                'priority',
                ['task', 'id', 'title', 'notes'],
            ]
        ]);
    }
}