<?php

namespace OrangeHRM\ZkTeco\Service;

use Exception;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\ZkTecoConfig;
use OrangeHRM\ZkTeco\Dao\ZkTecoDao;

class ZkTecoService
{
    protected ?ZkTecoDao $zkTecoDao = null;

    public function getZkTecoDao(): ZkTecoDao
    {
        if (is_null($this->zkTecoDao)) {
            $this->zkTecoDao = new ZkTecoDao();
        }

        return $this->zkTecoDao;
    }

    public function testConnection(ZkTecoConfig $config): array
    {
        try {
            return $this->getZkTecoDao()->testConnection($config);
        } catch (Exception) {
            return [
                'status' => 'error',
                'message' => 'Connection NOT successful',
            ];
        }
    }

    /**
     * @throws DaoException
     */
    public function saveConfig(ZkTecoConfig $config): ZkTecoConfig
    {
        return $this->getZkTecoDao()->saveConfig($config);
    }

    /**
     * @throws DaoException
     */
    public function getConfig(): ?ZkTecoConfig
    {
        return $this->getZkTecoDao()->getConfig();
    }
}