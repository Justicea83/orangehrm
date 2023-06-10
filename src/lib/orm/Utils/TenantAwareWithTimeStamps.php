<?php

namespace OrangeHRM\ORM\Utils;

use Carbon\Carbon;
use DateTime;
use OrangeHRM\ORM\Tenancy\TenantAware;
use Doctrine\ORM\Mapping as ORM;

class TenantAwareWithTimeStamps extends TenantAware
{
    /**
     * @var string|null
     *
     * @ORM\Column(name="created_at", type="string", nullable=true)
     */
    protected ?string $createdAt = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="updated_at", type="string", nullable=true)
     */
    protected ?string $updatedAt = null;

    /**
     * @param string|null $createdAt
     */
    public function setCreatedAt(?string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @param string|null $updatedAt
     */
    public function setUpdatedAt(?string $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return DateTime|null
     */
    public function getCreatedAt(): ?string
    {
        return Carbon::parse($this->createdAt)->toDateTimeString();
    }

    /**
     * @return DateTime|null
     */
    public function getUpdatedAt(): ?string
    {
        return Carbon::parse($this->updatedAt)->toDateTimeString();
    }
}