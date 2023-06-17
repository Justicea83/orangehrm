<?php

namespace OrangeHRM\Admin\Traits\Service;

use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Framework\Services;
use OrangeHRM\Onboarding\Service\TaskService;

trait TaskServiceTrait
{
    use ServiceContainerTrait;

    public function getTaskService(): TaskService
    {
        return $this->getContainer()->get(Services::TASK_SERVICE);
    }
}