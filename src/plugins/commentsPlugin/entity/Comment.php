<?php

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;
use OrangeHRM\Entity\Decorator\CommentDecorator;
use OrangeHRM\Entity\Decorator\DecoratorTrait;
use OrangeHRM\ORM\Utils\TenantAwareWithTimeStamps;

/**
 * @method CommentDecorator getDecorator()
 *
 * @ORM\Table(name="comments")
 * @ORM\Entity
 */
class Comment extends TenantAwareWithTimeStamps
{
    use DecoratorTrait;
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(name="model_id", type="integer", nullable=false)
     */
    private int $modelId;

    /**
     * @ORM\Column(name="model_type", type="string", nullable=false)
     */
    private string $modelType;

    /**
     * @ORM\Column(name="body", type="string", nullable=true)
     */
    private ?string $body = null;

    /**
     * @ORM\Column(name="parent_id", type="integer", nullable=true)
     */
    private ?int $parentId = null;

    /**
     * @ORM\Column(name="emp_number", type="integer", nullable=true)
     */
    private ?int $empNumber;

    /**
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Employee", inversedBy="comments")
     * @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number", nullable=true)
     */
    private ?Employee $employee = null;

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
     * @return int
     */
    public function getModelId(): int
    {
        return $this->modelId;
    }

    /**
     * @param int $modelId
     */
    public function setModelId(int $modelId): void
    {
        $this->modelId = $modelId;
    }

    /**
     * @return string
     */
    public function getModelType(): string
    {
        return $this->modelType;
    }

    /**
     * @param string $modelType
     */
    public function setModelType(string $modelType): void
    {
        $this->modelType = $modelType;
    }

    /**
     * @return string|null
     */
    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * @param string|null $body
     */
    public function setBody(?string $body): void
    {
        $this->body = $body;
    }

    /**
     * @return int|null
     */
    public function getParentId(): ?int
    {
        return $this->parentId;
    }

    /**
     * @param int|null $parentId
     */
    public function setParentId(?int $parentId): void
    {
        $this->parentId = $parentId;
    }

    /**
     * @return Employee|null
     */
    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    /**
     * @param Employee|null $employee
     */
    public function setEmployee(?Employee $employee): void
    {
        $this->employee = $employee;
    }
}