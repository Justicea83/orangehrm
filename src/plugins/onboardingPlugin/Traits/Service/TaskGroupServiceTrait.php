<?php

namespace OrangeHRM\Onboarding\Traits\Service;

use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Framework\Services;
use OrangeHRM\Onboarding\Service\TaskGroupService;

trait TaskGroupServiceTrait
{
    use ServiceContainerTrait;

    public function getTaskGroupService(): TaskGroupService
    {
        return $this->getContainer()->get(Services::TASK_GROUP_SERVICE);
    }
}