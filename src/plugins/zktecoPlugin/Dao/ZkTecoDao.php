<?php

namespace OrangeHRM\ZkTeco\Dao;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Core\Traits\LoggerTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Subunit;
use OrangeHRM\Entity\ZkTecoConfig;
use Exception;
use OrangeHRM\ZkTeco\Reports\TransactionReport;
use OrangeHRM\ZkTeco\Reports\ZkTecoApiResponse;

class ZkTecoDao extends BaseDao
{
    use LoggerTrait;

    /**
     * Get the first configuration
     *
     * @return ZkTecoConfig|null
     * @throws DaoException
     */
    public function getConfig(): ?ZkTecoConfig
    {
        try {
            $config = $this->getRepository(ZkTecoConfig::class)->findOneBy([]);
            return $config instanceof ZkTecoConfig ? $config : null;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Save or update the configuration, but keep extraData unchanged.
     *
     * @param ZkTecoConfig $config
     * @return ZkTecoConfig
     * @throws DaoException
     */
    public function saveConfig(ZkTecoConfig $config): ZkTecoConfig
    {
        try {
            $existingConfig = $this->getConfig();

            if ($existingConfig) {
                // Update relevant properties
                $existingConfig->setEnabled($config->isEnabled());
                $existingConfig->setOverrideSalary($config->isOverrideSalary());
                $existingConfig->setHost($config->getHost());
                $existingConfig->setPort($config->getPort());
                $existingConfig->setScheme($config->getScheme());
                $existingConfig->setAdminUsername($config->getAdminUsername());
                $existingConfig->setAdminPassword($config->getAdminPassword());
                $existingConfig->setSyncInterval($config->getSyncInterval());

                $config = $existingConfig;
            }

            $this->persist($config);
            return $config;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Save salary data inside extraData['salary'] array.
     *
     * @param array $salaryData
     * @return ZkTecoConfig|null
     * @throws DaoException
     */
    public function saveSalary(array $salaryData): ?ZkTecoConfig
    {
        try {
            $config = $this->getConfig();

            if (!$config) {
                throw new DaoException("ZkTecoConfig not found");
            }

            $extraData = $config->getExtraData() ?? [];

            // Ensure 'salary' key exists and add an ID to the new salary entry
            if (!isset($extraData['salary'])) {
                $extraData['salary'] = [];
            }

            $salaryData['id'] = uniqid('salary_', true); // Generate a unique ID
            $extraData['salary'][] = $salaryData;
            $config->setExtraData($extraData);

            $this->persist($config);
            return $config;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Delete multiple salary entries by their IDs from extraData['salary']
     *
     * @param array $salaryIds
     * @return ZkTecoConfig|null
     * @throws DaoException
     */
    public function deleteSalariesByIds(array $salaryIds): ?ZkTecoConfig
    {
        try {
            $config = $this->getConfig();

            if (!$config) {
                throw new DaoException("ZkTecoConfig not found");
            }

            // Decode extraData from JSON
            $extraData = $config->getExtraData();

            if (!isset($extraData['salary']) || !is_array($extraData['salary'])) {
                throw new DaoException("No salary records found");
            }

            // Ensure `salary` remains an indexed array after deletion
            $extraData['salary'] = array_values(array_filter($extraData['salary'], function ($entry) use ($salaryIds) {
                return !in_array($entry['id'] ?? '', $salaryIds, true);
            }));

            // Re-encode the extraData back to JSON before saving
            $config->setExtraData($extraData);

            $this->persist($config);

            return $config;
        } catch (\Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }


    /**
     * Edit a salary entry by ID in extraData['salary']
     *
     * @param string $salaryId
     * @param array $updatedData
     * @return ZkTecoConfig|null
     * @throws DaoException
     */
    public function editSalaryById(string $salaryId, array $updatedData): ?ZkTecoConfig
    {
        try {
            $config = $this->getConfig();

            if (!$config) {
                throw new DaoException("ZkTecoConfig not found");
            }

            $extraData = $config->getExtraData() ?? [];

            if (!isset($extraData['salary'])) {
                throw new DaoException("No salary records found");
            }

            // Find and update the salary entry with the given ID
            $extraData['salary'] = array_values(array_map(function ($entry) use ($salaryId, $updatedData) {
                if ($entry['id'] === $salaryId) {
                    return array_merge($entry, $updatedData); // Merge updated data
                }
                return $entry;
            }, $extraData['salary']));

            $config->setExtraData($extraData);
            $this->persist($config);

            return $config;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }


    /**
     * Test connection by making a request to the configured host and port.
     *
     * @return array
     * @throws DaoException|GuzzleException
     */
    public function testConnection(ZkTecoConfig $config): array
    {
        try {
            if (!$config->getHost() || !$config->getPort()) {
                throw new DaoException("Host or port not configured");
            }

            $url = sprintf("%s://%s:%s/jwt-api-token-auth/", $config->getScheme(), $config->getHost(), $config->getPort());

            $client = new Client();
            $response = $client->post($url, [
                'json' => [
                    'username' => $config->getAdminUsername(),
                    'password' => $config->getAdminPassword(),
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'http_errors' => false, // Prevent Guzzle from throwing exceptions for non-2xx responses
            ]);

            $statusCode = $response->getStatusCode();
            $body = json_decode($response->getBody()->getContents(), true);

            if ($statusCode === 200 && isset($body['token'])) {
                return [
                    'status' => 'success',
                    'token' => $body['token'],
                ];
            }

            return [
                'status' => 'error',
                'message' => 'Connection NOT successful',
            ];
        } catch (RequestException $e) {
            throw new DaoException("Request failed: " . $e->getMessage(), $e->getCode(), $e);
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function fetchTransactions(PunchPairFilterParams $params): ZkTecoApiResponse
    {
        $query_params = [
            'page' => $params->getPage(),
            'page_size' => $params->getLimit(),
        ];

        $reportMode = $params->getReportMode();
        $date = $params->getDate();

        if ($reportMode === 'daily') {
            $query_params['start_date'] = $date;
            $query_params['end_date'] = date('Y-m-d', strtotime($date . ' +1 day'));
        } elseif ($reportMode === 'monthly') {
            $query_params['start_date'] = date('Y-m-01', strtotime($date));
            $query_params['end_date'] = date('Y-m-t', strtotime($date));
        } elseif ($reportMode === 'yearly') {
            $query_params['start_date'] = date('Y-01-01', strtotime($date));
            $query_params['end_date'] = date('Y-12-31', strtotime($date));
        } else {
            // Default to today's date if report mode is not recognized
            $query_params['start_date'] = date('Y-m-d');
            $query_params['end_date'] = date('Y-m-d');
        }

        if (count($params->getDepartments()) > 0) {
            $departments = array_map(function (Subunit $subunit) {
                $extraData = $subunit->getExtraData();
                return $extraData['zk_external_id'] ?? null;
            }, $this->getRepository(Subunit::class)
                ->createQueryBuilder('s')
                ->where('s.id IN (:departments)')
                ->setParameter('departments', $params->getDepartments())
                ->getQuery()
                ->getResult());

            $departments = array_filter($departments);

            if (!empty($departments)) {
                $query_params['departments'] = implode(',', $departments);
            }
        }

        if (count($params->getEmployees()) > 0) {
            $employees = array_map(function (Employee $employee) {
                $extraData = $employee->getExtraData();
                return $extraData['zk_external_id'] ?? null;
            }, $this->getRepository(Employee::class)
                ->createQueryBuilder('s')
                ->where('s.empNumber IN (:employees)')
                ->setParameter('employees', $params->getEmployees())
                ->getQuery()
                ->getResult());

            $employees = array_filter($employees);

            if (!empty($employees)) {
                $query_params['employees'] = implode(',', $employees);
            }
        }


        $response = TransactionReport::instance()->fetchTransactions($query_params);

        if ($params->getReportMode() == 'daily') {
            $response->setData(TransactionReport::instance()->generateDTRReport($response->getData(), $query_params['start_date']));
        }

        return $response;
    }
}