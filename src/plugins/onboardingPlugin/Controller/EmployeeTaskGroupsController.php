<?php

namespace OrangeHRM\Onboarding\Controller;

use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\CorporateBranding\Traits\ThemeServiceTrait;
use OrangeHRM\Framework\Http\Request;

class EmployeeTaskGroupsController extends AbstractVueController
{
    use ThemeServiceTrait, AuthUserTrait;

    public function preRender(Request $request): void
    {
        $component = new Component('employee-task-groups');
        $component->addProp(new Prop('theme', Prop::TYPE_ARRAY, $this->getThemeService()->getCurrentThemeVariables()));
        $component->addProp(new Prop('user-id', Prop::TYPE_STRING, $this->getAuthUser()->getEmpNumber()));

        $this->setComponent($component);
    }
}