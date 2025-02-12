<?php

namespace OrangeHRM\ZkTeco\Controller\Attendance;

use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Framework\Http\Request;

class AttendancePunchPairController extends AbstractVueController
{
    public function preRender(Request $request): void
    {
        $component = new Component('punch-pair-report');
        $this->setComponent($component);
    }
}