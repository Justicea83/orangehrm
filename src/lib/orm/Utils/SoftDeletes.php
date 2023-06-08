<?php

namespace OrangeHRM\ORM\Utils;

use Carbon\Carbon;

trait SoftDeletes
{
    /**
     * @var string|null
     *
     * @ORM\Column(name="deleted_at", type="string", nullable=true)
     */
    protected ?string $deletedAt = null;

    /**
     * @return string|null
     */
    public function getDeletedAt(): ?string
    {
        return Carbon::parse($this->deletedAt)->toDateString();
    }

    /**
     * @param string|null $deletedAt
     */
    public function setDeletedAt(?string $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }
}