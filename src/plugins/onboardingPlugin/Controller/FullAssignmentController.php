<?php

namespace OrangeHRM\Onboarding\Controller;

use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\CorporateBranding\Traits\ThemeServiceTrait;
use OrangeHRM\Framework\Http\Request;

class FullAssignmentController extends AbstractVueController
{
    use AuthUserTrait, ThemeServiceTrait;

    public function preRender(Request $request): void
    {
        $component = new Component('full-assignment');
        $component->addProp(new Prop('assignment-id', Prop::TYPE_STRING, $request->attributes->getInt('id')));
        $component->addProp(new Prop('creator-id', Prop::TYPE_STRING, $this->getAuthUser()->getEmpNumber()));
        $component->addProp(new Prop('theme', Prop::TYPE_ARRAY, $this->getThemeService()->getCurrentThemeVariables()));
        $this->setComponent($component);
    }
}