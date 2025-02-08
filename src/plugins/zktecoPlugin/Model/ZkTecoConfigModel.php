<?php

namespace OrangeHRM\ZkTeco\Model;

use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Entity\ZkTecoConfig;

class ZkTecoConfigModel implements Normalizable
{
    private ?ZkTecoConfig $zkTecoConfig;

    function __construct(?ZkTecoConfig $zkTecoConfig)
    {
        $this->zkTecoConfig = $zkTecoConfig;
    }

    public function toArray(): array
    {
        if (is_null($this->zkTecoConfig)) {
            return [];
        }
        $salaries = [];
        if (isset($this->zkTecoConfig->getExtraData()['salary'])) {
            $salaries = array_map(function ($salary) {
                $salary['amount'] = $salary['salaryAmount'];
                $salary['salaryAmount'] = sprintf('%s %s', $salary['currencyId'], number_format($salary['salaryAmount'], 2));
                return $salary;
            }, $this->zkTecoConfig->getExtraData()['salary']);
        }
        return [
            'enabled' => $this->zkTecoConfig->isEnabled(),
            'host' => $this->zkTecoConfig->getHost(),
            'syncing' => $this->zkTecoConfig->isSyncing(),
            'overrideSalary' => $this->zkTecoConfig->isOverrideSalary(),
            'lastSync' => $this->zkTecoConfig->getLastSync(),
            'scheme' => ucfirst($this->zkTecoConfig->getScheme() ?? ''),
            'port' => $this->zkTecoConfig->getPort(),
            'adminUsername' => $this->zkTecoConfig->getAdminUsername(),
            'adminPassword' => $this->zkTecoConfig->getAdminPassword(),
            'syncInterval' => $this->zkTecoConfig->getSyncInterval(),
            'salaries' => $salaries
        ];
    }
}