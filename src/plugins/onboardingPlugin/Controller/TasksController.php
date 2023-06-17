<?php

namespace OrangeHRM\Onboarding\Controller;

use OrangeHRM\Admin\Traits\Service\TaskServiceTrait;
use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Framework\Http\Request;

class TasksController extends AbstractVueController
{
    use UserRoleManagerTrait;
    use TaskServiceTrait;

    public function preRender(Request $request): void
    {
        $component = new Component('task-list');
        $component->addProp(
            new Prop(
                'unselectable-task-ids',
                Prop::TYPE_ARRAY,
                $this->getTaskService()->getUnDeletableTaskIds()
            )
        );
        $this->setComponent($component);
    }
}