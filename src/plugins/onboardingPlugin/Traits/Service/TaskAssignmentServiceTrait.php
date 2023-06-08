<?php

namespace OrangeHRM\Onboarding\Traits\Service;

use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Framework\Services;
use OrangeHRM\Onboarding\Service\TaskAssignmentService;

trait TaskAssignmentServiceTrait
{
    use ServiceContainerTrait;

    public function getTaskAssignmentService(): TaskAssignmentService
    {
        return $this->getContainer()->get(Services::TASK_ASSIGNMENT_SERVICE);
    }
}