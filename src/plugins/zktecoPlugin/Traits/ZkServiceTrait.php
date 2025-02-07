<?php

namespace OrangeHRM\ZkTeco\Traits;

use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\ZkTeco\Service\ZkTecoService;
use OrangeHRM\Framework\Services;

trait ZkServiceTrait
{
    use ServiceContainerTrait;

    public function getZkTecoService(): ZkTecoService
    {
        return $this->getContainer()->get(Services::ZKTECO_SERVICE);
    }
}