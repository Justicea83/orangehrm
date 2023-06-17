<?php

namespace OrangeHRM\Onboarding\Traits\Service;

use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Framework\Services;
use OrangeHRM\Onboarding\Service\TaskTypeService;

trait TaskTypeServiceTrait
{
    use ServiceContainerTrait;

    public function getTaskTypeService(): TaskTypeService
    {
        return $this->getContainer()->get(Services::TASK_TYPE_SERVICE);
    }
}