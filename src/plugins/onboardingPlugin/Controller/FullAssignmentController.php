<?php

namespace OrangeHRM\Onboarding\Controller;

use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Framework\Http\Request;

class FullAssignmentController extends AbstractVueController
{
    public function preRender(Request $request): void
    {
        $component = new Component('full-assignment');
        $component->addProp(new Prop('assignment-id', Prop::TYPE_STRING, $request->attributes->getInt('id')));
        $this->setComponent($component);
    }
}