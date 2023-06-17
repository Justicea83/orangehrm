<?php

namespace OrangeHRM\Onboarding\Dto;

use OrangeHRM\Core\Dto\FilterParams;

class TaskSearchFilterParams extends FilterParams
{
    public const ALLOWED_SORT_FIELDS = [
        'task.title',
        'task.type',
    ];

    protected ?string $title = null;
    protected ?int $type = null;
    protected ?string $types = null;

    public function __construct()
    {
        $this->setSortField('task.title');
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return int|null
     */
    public function getType(): ?int
    {
        return $this->type;
    }

    /**
     * @param int|null $type
     */
    public function setType(?int $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string|null
     */
    public function getTypes(): ?string
    {
        return $this->types;
    }

    /**
     * @param string|null $types
     */
    public function setTypes(?string $types): void
    {
        $this->types = $types;
    }
}