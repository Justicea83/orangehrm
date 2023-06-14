<?php

namespace OrangeHRM\Onboarding\Controller;

use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Framework\Http\Request;

class TaskGroupsController extends AbstractVueController
{
    public function preRender(Request $request): void
    {
        $component = new Component('task-groups');
        $this->setComponent($component);
    }
}