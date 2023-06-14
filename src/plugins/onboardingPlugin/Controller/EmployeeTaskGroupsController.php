<?php

namespace OrangeHRM\Onboarding\Controller;

use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Framework\Http\Request;

class EmployeeTaskGroupsController extends AbstractVueController
{
    public function preRender(Request $request): void
    {
        $component = new Component('employee-task-groups');
        $this->setComponent($component);
    }
}