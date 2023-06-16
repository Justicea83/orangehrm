<?php

namespace OrangeHRM\Onboarding\Controller;

use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Framework\Http\Request;

class SaveTaskTypeController extends AbstractVueController
{
    public function preRender(Request $request): void
    {
        if ($request->attributes->has('id')) {
            $component = new Component('edit-task-type');
            $component->addProp(new Prop('task-type-id', Prop::TYPE_STRING, $request->attributes->getInt('id')));
        } else {
            $component = new Component('save-task-type');
        }
        $this->setComponent($component);
    }
}