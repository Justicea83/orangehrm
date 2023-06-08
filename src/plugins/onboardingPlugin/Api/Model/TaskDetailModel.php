<?php

namespace OrangeHRM\Onboarding\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\ModelTrait;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Entity\Task;

class TaskDetailModel implements Normalizable
{
    use ModelTrait;

    public function __construct(Task $task)
    {
        $this->setEntity($task);
        $this->setFilters([
            'title',
            'notes',
            'id',
            'createdAt',
            'updatedAt',
            ['getTaskType', 'getId'],
            ['getTaskType', 'getName'],
        ]);

        $this->setAttributeNames(
            [
                'title',
                'notes',
                'id',
                'createdAt',
                'updatedAt',
                ['taskType', 'id'],
                ['taskType', 'name'],
            ]
        );
    }
}