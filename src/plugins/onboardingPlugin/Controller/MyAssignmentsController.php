<?php

namespace OrangeHRM\Onboarding\Controller;

use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Framework\Http\Request;

class MyAssignmentsController extends AbstractVueController
{
    public function preRender(Request $request): void
    {
        $component = new Component('my-assignments');
        $this->setComponent($component);
    }
}