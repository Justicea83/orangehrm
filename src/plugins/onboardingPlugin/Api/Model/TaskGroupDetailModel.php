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
            'id',
            'isCompleted',
            'dueDate',
            'priority',
            ['getTask', 'getId',],
            ['getTask', 'getTitle',],
            ['getTask', 'getNotes'],
            ['getTask', 'getCreatedAt'],

        ]);

        $this->setAttributeNames([

            'id',
            'completed',
            'dueDate',
            'priority',
            ['task', 'id'],
            ['task', 'title',],
            ['task', 'notes'],
            ['task', 'createdAt'],
        ]);
    }
}