<?php

namespace OrangeHRM\Admin\Traits\Service;

use OrangeHRM\Admin\Service\OrganizationService;
use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Framework\Services;

trait OrganizationServiceTrait
{
    use ServiceContainerTrait;

    public function getOrganizationService(): OrganizationService
    {
        return $this->getContainer()->get(Services::ORGANIZATION_SERVICE);
    }
}