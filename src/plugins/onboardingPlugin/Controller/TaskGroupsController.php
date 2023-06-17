<?php

namespace OrangeHRM\Onboarding\Controller;

use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\CorporateBranding\Traits\ThemeServiceTrait;
use OrangeHRM\Framework\Http\Request;

class TaskGroupsController extends AbstractVueController
{
    use ThemeServiceTrait;

    public function preRender(Request $request): void
    {
        $component = new Component('task-groups');
        $component->addProp(new Prop('theme', Prop::TYPE_ARRAY, $this->getThemeService()->getCurrentThemeVariables()));

        $this->setComponent($component);
    }
}