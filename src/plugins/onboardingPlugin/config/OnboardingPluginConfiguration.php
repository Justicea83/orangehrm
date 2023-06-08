<?php

use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\PluginConfigurationInterface;
use OrangeHRM\Framework\Services;
use OrangeHRM\Onboarding\Service\TaskAssignmentService;
use OrangeHRM\Onboarding\Service\TaskService;
use OrangeHRM\Onboarding\Service\TaskTypeService;

class OnboardingPluginConfiguration implements PluginConfigurationInterface
{
    use ServiceContainerTrait;

    public function initialize(Request $request): void
    {
        $this->getContainer()->register(
            Services::TASK_SERVICE,
            TaskService::class
        );

        $this->getContainer()->register(
            Services::TASK_ASSIGNMENT_SERVICE,
            TaskAssignmentService::class
        );

        $this->getContainer()->register(
            Services::TASK_TYPE_SERVICE,
            TaskTypeService::class
        );
    }
}
