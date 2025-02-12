<?php

namespace OrangeHRM\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use OrangeHRM\ORM\Tenancy\TenantAware;

/**
 * @ORM\Table(name="ohrm_zk_teco_config")
 * @ORM\Entity
 */
class ZkTecoConfig extends TenantAware
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", length=10, options={"unsigned" : true})
     */
    private int $id;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean", options={"default" : false})
     */
    private bool $enabled = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="override_salary", type="boolean", options={"default" : false})
     */
    private bool $overrideSalary = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="syncing", type="boolean", options={"default" : false})
     */
    private bool $syncing = false;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="last_sync", type="datetime", nullable=true)
     */
    private ?DateTime $lastSync = null;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="force_sync_at", type="datetime", nullable=true)
     */
    private ?DateTime $forceSyncAt = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="host", type="string", length=255, nullable=true)
     */
    private ?string $host = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="scheme", type="string", length=255, nullable=true)
     */
    private ?string $scheme = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="port", type="string", length=255, nullable=true)
     */
    private ?string $port = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="admin_username", type="string", length=255, nullable=true)
     */
    private ?string $adminUsername = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="admin_password", type="string", length=255, nullable=true)
     */
    private ?string $adminPassword = null;

    /**
     * @var int|null
     *
     * @ORM\Column(name="sync_interval", type="integer", length=10, nullable=true, options={"unsigned" : true})
     */
    private ?int $syncInterval = null;

    /**
     * @var array|null
     *
     * @ORM\Column(name="extra_data", type="json", nullable=true)
     */
    private ?array $extraData = null;

    // Getters and Setters
    public function getId(): int
    {
        return $this->id;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function isOverrideSalary(): bool
    {
        return $this->overrideSalary;
    }

    public function setOverrideSalary(bool $overrideSalary): void
    {
        $this->overrideSalary = $overrideSalary;
    }

    public function isSyncing(): bool
    {
        return $this->syncing;
    }

    public function setSyncing(bool $syncing): void
    {
        $this->syncing = $syncing;
    }

    public function getLastSync(): ?DateTime
    {
        return $this->lastSync;
    }

    public function setLastSync(?DateTime $lastSync): void
    {
        $this->lastSync = $lastSync;
    }

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function getScheme(): ?string
    {
        return $this->scheme;
    }

    public function setHost(?string $host): void
    {
        $this->host = $host;
    }

    public function getPort(): ?string
    {
        return $this->port;
    }

    public function setPort(?string $port): void
    {
        $this->port = $port;
    }

    public function getAdminUsername(): ?string
    {
        return $this->adminUsername;
    }

    public function setAdminUsername(?string $adminUsername): void
    {
        $this->adminUsername = $adminUsername;
    }

    public function getAdminPassword(): ?string
    {
        return $this->adminPassword;
    }

    public function setAdminPassword(?string $adminPassword): void
    {
        $this->adminPassword = $adminPassword;
    }

    public function getSyncInterval(): ?int
    {
        return $this->syncInterval;
    }

    public function setSyncInterval(?int $syncInterval): void
    {
        $this->syncInterval = $syncInterval;
    }

    public function getExtraData(): ?array
    {
        return $this->extraData;
    }

    public function setExtraData(?array $extraData): void
    {
        $this->extraData = $extraData;
    }

    public function setScheme(?string $scheme): void
    {
        $this->scheme = $scheme;
    }

    public function getForceSyncAt(): ?DateTime
    {
        return $this->forceSyncAt;
    }

    public function setForceSyncAt(?DateTime $forceSyncAt): void
    {
        $this->forceSyncAt = $forceSyncAt;
    }
}
