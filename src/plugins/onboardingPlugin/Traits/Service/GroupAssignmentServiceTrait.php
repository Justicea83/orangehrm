<?php

namespace OrangeHRM\Onboarding\Traits\Service;

use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Framework\Services;
use OrangeHRM\Onboarding\Service\GroupAssignmentService;

trait GroupAssignmentServiceTrait
{
    use ServiceContainerTrait;

    public function getGroupAssignmentService(): GroupAssignmentService
    {
        return $this->getContainer()->get(Services::GROUP_ASSIGNMENT_SERVICE);
    }
}