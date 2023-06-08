<?php

namespace OrangeHRM\Onboarding\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\ModelTrait;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Entity\TaskType;

class TaskTypeModel implements Normalizable
{
    use ModelTrait;

    public function __construct(TaskType $taskType)
    {
        $this->setEntity($taskType);
        $this->setFilters([
            'name',
            'id',
        ]);

        $this->setAttributeNames(
            [
                'name',
                'id',
            ]
        );
    }
}