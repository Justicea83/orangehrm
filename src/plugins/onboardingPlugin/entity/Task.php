<?php

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;
use OrangeHRM\Entity\Decorator\DecoratorTrait;
use OrangeHRM\Entity\Decorator\TaskDecorator;
use OrangeHRM\ORM\Utils\TenantAwareWithTimeStamps;

/**
 * @method TaskDecorator getDecorator()
 *
 * @ORM\Table(name="tasks")
 * @ORM\Entity
 */
class Task extends TenantAwareWithTimeStamps
{
    use DecoratorTrait;

    public const TYPE_ONBOARDING = 0;
    public const TYPE_OFFBOARDING = 1;
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private string $title;

    /**
     * @var string|null
     *
     * @ORM\Column(name="notes", type="string")
     */
    private ?string $notes;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer")
     */
    private int $type = self::TYPE_ONBOARDING;

    /**
     * @var bool
     *
     * @ORM\Column(name="disabled", type="integer")
     */
    private bool $disabled = false;

    /**
     * @var JobTitle|null
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\JobTitle", inversedBy="tasks")
     * @ORM\JoinColumn(name="job_id", referencedColumnName="id", nullable=true)
     */
    private ?JobTitle $jobTitle = null;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): void
    {
        $this->notes = $notes;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType(int $type): void
    {
        $this->type = $type;
    }

    /**
     * @return bool
     */
    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    /**
     * @param bool $disabled
     */
    public function setDisabled(bool $disabled): void
    {
        $this->disabled = $disabled;
    }

    /**
     * @return JobTitle|null
     */
    public function getJobTitle(): ?JobTitle
    {
        return $this->jobTitle;
    }

    /**
     * @param JobTitle|null $jobTitle
     */
    public function setJobTitle(?JobTitle $jobTitle): void
    {
        $this->jobTitle = $jobTitle;
    }

    /**
     * @return string
     */
    public function getTypeText(): string
    {
        if ($this->type === self::TYPE_ONBOARDING) {
            return 'Onboarding';
        }
        return 'Offboarding';
    }

}