<?php

namespace OrangeHRM\ZkTeco\Reports;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\LoggerTrait;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Entity\ZkTecoConfig;
use Exception;
use OrangeHRM\Framework\Http\Session\Session;
use OrangeHRM\Framework\Services;
use Throwable;

abstract class AbstractZkTecoReport
{
    use LoggerTrait, ServiceContainerTrait, EntityManagerHelperTrait, AuthUserTrait;

    /**
     * @var Client
     */
    protected Client $client;

    protected static array $localTokenCache = [];

    public function __construct()
    {
        $this->client = new Client();
    }

    private function getZkTecoConfig(): ?ZkTecoConfig
    {
        $config = $this->getRepository(ZkTecoConfig::class)->findOneBy([]);
        return $config instanceof ZkTecoConfig ? $config : null;
    }

    protected function call(
        string $endpoint,
        ?array $payload = null,
        string $method = 'GET'
    ): ZkTecoApiResponse
    {
        $organization = $this->getAuthUser()->getOrgId();
        $responseType = new ZkTecoApiResponse();
        $config = $this->getZkTecoConfig();

        // Build base URI
        $baseUri = sprintf('%s://%s:%s', $config->getScheme(), $config->getHost(), $config->getPort());

        // Retrieve token from cache or generate a new one
        $token = $this->getCachedToken($organization);

        // Log the request.
        $this->getLogger()->info('ZKTeco Request', [
            'endpoint' => $endpoint,
            'method' => $method,
            'payload' => $payload,
        ]);

        try {
            // Prepare request options
            $requestOptions = [
                'headers' => [
                    'Authorization' => 'JWT ' . $token,
                    'Accept' => 'application/json'
                ],
            ];

            // If it's a GET request, attach parameters as query params
            if ($method === 'GET' && !empty($payload)) {
                $endpoint .= '?' . http_build_query($payload);
            } elseif (!empty($payload)) {
                // Otherwise, send payload as JSON for POST/PUT requests
                $requestOptions['json'] = $payload;
            }

            // Execute request
            $response = $this->client->request($method, $baseUri . $endpoint, $requestOptions);

            $statusCode = $response->getStatusCode();
            $responseData = json_decode($response->getBody()->getContents(), true);

            if ($statusCode === 401) {
                $this->getLogger()->info('ZKTeco token expired; refreshing token and retrying');
                $this->refreshToken($organization);
                return $this->call($endpoint, $payload, $method);
            }

            if ($statusCode >= 200 && $statusCode < 300) {
                $responseType->setCount($responseData['count'] ?? 0)
                    ->setCode($responseData['code'] ?? $statusCode)
                    ->setNext($responseData['next'] ?? null)
                    ->setPrevious($responseData['previous'] ?? null)
                    ->setMsg($responseData['msg'] ?? '')
                    ->setData($responseData['data'] ?? []);
            } else {
                $this->getLogger()->error('ZKTeco Non-2xx response', [
                    'statusCode' => $statusCode,
                    'body' => $responseData
                ]);
            }
        } catch (GuzzleException $e) {
            $this->getLogger()->error('ZKTeco GuzzleException', ['exception' => $e->getMessage()]);
        } catch (Throwable $t) {
            $this->getLogger()->error('ZKTeco Throwable', ['exception' => $t->getMessage()]);
        }

        return $responseType;
    }


    /**
     * Retrieves the token from cache.
     * If it doesn't exist, calls getToken to retrieve a fresh one.
     */
    private function getCachedToken(?int $organization): string
    {
        /** @var Session $session */
        $session = $this->getContainer()->get(Services::SESSION);

        $cacheKey = sprintf('%s_zkteco_token', $organization);

        // 1) Try session-based cache first
        if ($session->has($cacheKey)) {
            return $session->get($cacheKey);
        }

        // 2) Fallback to local static array
        if (isset(self::$localTokenCache[$cacheKey])) {
            return self::$localTokenCache[$cacheKey];
        }

        // 3) No cached token; fetch a new one
        $newToken = $this->getToken($organization);
        $session->set($cacheKey, $newToken);
        self::$localTokenCache[$cacheKey] = $newToken;

        return $newToken;
    }

    /**
     * Fetches a new token from the ZKTeco API.
     * Once fetched, it is stored so subsequent calls can reuse it.
     */
    private function getToken(?int $organization): string
    {
        /** @var Session $session */
        $session = $this->getContainer()->get(Services::SESSION);

        $config = $this->getZkTecoConfig();
        $baseUri = sprintf('%s://%s:%s', $config->getScheme(), $config->getHost(), $config->getPort());

        try {
            $response = $this->client->post($baseUri . '/jwt-api-token-auth/', [
                'json' => [
                    'username' => $config->getAdminUsername(),
                    'password' => $config->getAdminPassword(),
                ],
            ]);

            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                $json = json_decode($response->getBody()->getContents(), true);
                $token = $json['token'] ?? '';
                if ($token) {
                    $cacheKey = sprintf('%s_zkteco_token', $organization);
                    $session->set($cacheKey, $token);
                    self::$localTokenCache[$cacheKey] = $token;
                    return $token;
                }
            }
        } catch (GuzzleException $e) {
            $this->getLogger()->error('ZKTeco Token Retrieval GuzzleException', ['exception' => $e->getMessage()]);
        } catch (Throwable $t) {
            $this->getLogger()->error('ZKTeco Token Retrieval Throwable', ['exception' => $t->getMessage()]);
        }

        // If no token retrieved
        $this->getLogger()->error('Failed to retrieve ZKTeco token');
        return '';
    }

    /**
     * Force refresh token by calling getToken again and invalidating the cached version.
     * @throws Exception
     */
    private function refreshToken(?int $organization): void
    {
        /** @var Session $session */
        $session = $this->getContainer()->get(Services::SESSION);

        $cacheKey = sprintf('%s_zkteco_token', $organization);
        if ($session->has($cacheKey)) {
            $session->remove($cacheKey);
        }
        unset(self::$localTokenCache[$cacheKey]);
        $this->getToken($organization);
    }
}