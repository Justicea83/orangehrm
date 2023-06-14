<?php

namespace OrangeHRM\Onboarding\Controller;

use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Framework\Http\Request;

class TaskTypesController extends AbstractVueController
{
    public function preRender(Request $request): void
    {
        $component = new Component('task-types');
        $this->setComponent($component);
    }
}