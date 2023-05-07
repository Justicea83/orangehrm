<?php

namespace OrangeHRM\Admin\Controller;

use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Framework\Http\Request;

class SaveTaskController extends AbstractVueController
{
    public function preRender(Request $request): void
    {
        if ($request->attributes->has('id')) {
            $component = new Component('edit-task');
            $component->addProp(new Prop('task-id', Prop::TYPE_STRING, $request->attributes->getInt('id')));
        } else {
            $component = new Component('save-task');
        }
        $this->setComponent($component);
    }
}