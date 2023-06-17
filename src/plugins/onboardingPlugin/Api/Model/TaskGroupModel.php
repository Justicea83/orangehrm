<?php

namespace OrangeHRM\Onboarding\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\ModelTrait;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Entity\TaskGroup;

class TaskGroupModel implements Normalizable
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
            ]
        ]);

        $this->setAttributeNames([
            [
                'id',
                'isCompleted',
                'dueDate',
                'priority',
            ]
        ]);
    }
}