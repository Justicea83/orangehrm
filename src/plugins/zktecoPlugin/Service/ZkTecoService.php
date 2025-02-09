<?php

namespace OrangeHRM\ZkTeco\Service;

use Exception;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\ZkTecoConfig;
use OrangeHRM\ZkTeco\Dao\PunchPairFilterParams;
use OrangeHRM\ZkTeco\Dao\ZkTecoDao;
use OrangeHRM\ZkTeco\Reports\TransactionReport;
use OrangeHRM\ZkTeco\Reports\ZkTecoApiResponse;

class ZkTecoService
{
    protected ?ZkTecoDao $zkTecoDao = null;

    private function getZkTecoDao(): ZkTecoDao
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

    /**
     * @throws DaoException
     */
    public function saveSalary(array $salaryData): ?ZkTecoConfig
    {
        return $this->getZkTecoDao()->saveSalary($salaryData);
    }

    /**
     * @throws DaoException
     */
    public function deleteSalariesByIds(array $salaryIds): ?ZkTecoConfig
    {
        return $this->getZkTecoDao()->deleteSalariesByIds($salaryIds);
    }

    /**
     * @throws DaoException
     */
    public function editSalaryById(string $salaryId, array $updatedData): ?ZkTecoConfig
    {
        return $this->getZkTecoDao()->editSalaryById($salaryId, $updatedData);
    }

    public function fetchTransactions(PunchPairFilterParams $params): ZkTecoApiResponse
    {
        return $this->getZkTecoDao()->fetchTransactions($params);
    }
}