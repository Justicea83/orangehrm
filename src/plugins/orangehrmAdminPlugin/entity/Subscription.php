<?php

namespace OrangeHRM\Entity;

use DateTimeInterface;
use OrangeHRM\ORM\Tenancy\TenantAware;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ohrm_subscription")
 */
class Subscription extends TenantAware
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(name="status", type="string", nullable=true)
     */
    private ?string $status = null;

    /**
     * @ORM\Column(name="max_licenses", type="integer", options={"default": 0})
     */
    private int $maxLicenses = 0;

    /**
     * @ORM\Column(name="current_period_start", type="datetime", nullable=true)
     */
    private ?DateTimeInterface $currentPeriodStart = null;

    /**
     * @ORM\Column(name="current_period_end", type="datetime", nullable=true)
     */
    private ?DateTimeInterface $currentPeriodEnd = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    public function getMaxLicenses(): int
    {
        return $this->maxLicenses;
    }

    public function setMaxLicenses(int $maxLicenses): void
    {
        $this->maxLicenses = $maxLicenses;
    }

    public function getCurrentPeriodStart(): ?DateTimeInterface
    {
        return $this->currentPeriodStart;
    }

    public function setCurrentPeriodStart(?DateTimeInterface $currentPeriodStart): void
    {
        $this->currentPeriodStart = $currentPeriodStart;
    }

    public function getCurrentPeriodEnd(): ?DateTimeInterface
    {
        return $this->currentPeriodEnd;
    }

    public function setCurrentPeriodEnd(?DateTimeInterface $currentPeriodEnd): void
    {
        $this->currentPeriodEnd = $currentPeriodEnd;
    }
}