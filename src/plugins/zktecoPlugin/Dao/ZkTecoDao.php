<?php

namespace OrangeHRM\ZkTeco\Dao;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Core\Traits\LoggerTrait;
use OrangeHRM\Entity\ZkTecoConfig;
use Exception;

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
     * Delete a salary entry by ID from extraData['salary']
     *
     * @param string $salaryId
     * @return ZkTecoConfig|null
     * @throws DaoException
     */
    public function deleteSalaryById(string $salaryId): ?ZkTecoConfig
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

            // Filter out the salary entry with the given ID
            $extraData['salary'] = array_filter($extraData['salary'], function ($entry) use ($salaryId) {
                return $entry['id'] !== $salaryId;
            });

            $config->setExtraData($extraData);
            $this->persist($config);

            return $config;
        } catch (Exception $e) {
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
            $extraData['salary'] = array_map(function ($entry) use ($salaryId, $updatedData) {
                if ($entry['id'] === $salaryId) {
                    return array_merge($entry, $updatedData); // Merge updated data
                }
                return $entry;
            }, $extraData['salary']);

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
}