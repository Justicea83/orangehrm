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
            'type',
            'notes',
            'id',
            'createdAt',
            'updatedAt',
            ['getJobTitle', 'getId'],
            ['getJobTitle', 'getJobTitleName'],
            ['getJobTitle', 'isDeleted'],
            ['getTypeText']
        ]);

        $this->setAttributeNames(
            [
                'title',
                'type',
                'notes',
                'id',
                'createdAt',
                'updatedAt',
                ['jobTitle', 'id'],
                ['jobTitle', 'title'],
                ['jobTitle', 'isDeleted'],
                ['typeText']
            ]
        );
    }
}