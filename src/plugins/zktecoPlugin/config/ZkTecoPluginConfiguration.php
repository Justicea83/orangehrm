<?php

use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\PluginConfigurationInterface;
use OrangeHRM\Admin\Service\OrganizationService;
use OrangeHRM\Framework\Services;
use OrangeHRM\Pim\Service\EmployeeSalaryService;
use OrangeHRM\Pim\Service\EmployeeService;
use OrangeHRM\ZkTeco\Service\ZkTecoService;

class ZkTecoPluginConfiguration implements PluginConfigurationInterface
{
    use ServiceContainerTrait;

    public function initialize(Request $request): void
    {
        $this->getContainer()->register(
            Services::EMPLOYEE_SERVICE,
            EmployeeService::class
        );
        $this->getContainer()->register(
            Services::EMPLOYEE_SALARY_SERVICE,
            EmployeeSalaryService::class
        );
        $this->getContainer()->register(
            Services::ORGANIZATION_SERVICE,
            OrganizationService::class
        );
        $this->getContainer()->register(
            Services::ZKTECO_SERVICE,
            ZkTecoService::class
        );
    }
}