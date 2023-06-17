<?php

namespace OrangeHRM\Onboarding\Controller;

use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Framework\Http\Request;

class CreateOnboardingController extends AbstractVueController
{
    public function preRender(Request $request): void
    {
        $component = new Component('create-onboarding');
        $this->setComponent($component);
    }
}