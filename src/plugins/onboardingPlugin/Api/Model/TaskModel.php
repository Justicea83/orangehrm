<?php

namespace OrangeHRM\Onboarding\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\ModelTrait;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Entity\Task;
use OrangeHRM\Entity\Employee;

/**
 * @OA\Schema(
 *     schema="Onboarding-TaskModel",
 *     @OA\Property(property="title", type="string"),
 *     @OA\Property(property="type", type="integer"),
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="createdAt", type="string"),
 *     @OA\Property(property="updatedAt", type="string"),
 * )
 */
class TaskModel implements Normalizable
{
    use ModelTrait;

    public function __construct(Task $task)
    {
        $this->setEntity($task);
        $this->setFilters([
            'title',
            'type',
            'id',
            'createdAt',
            'updatedAt',
        ]);

        $this->setAttributeNames(
            [
                'title',
                'type',
                'id',
            ]
        );
    }
}