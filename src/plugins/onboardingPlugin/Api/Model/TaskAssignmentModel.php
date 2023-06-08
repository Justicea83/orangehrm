<?php

namespace OrangeHRM\Onboarding\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\ModelTrait;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Entity\TaskAssignment;

class TaskAssignmentModel implements Normalizable
{
    use ModelTrait;

    public function __construct(TaskAssignment $taskAssignment) {
        $this->setEntity($taskAssignment);

        $this->setFilters([
            'id',
            'notes',
            'name'
        ]);

        $this->setAttributeNames([
            'id',
            'notes',
            'name'
        ]);
    }
}