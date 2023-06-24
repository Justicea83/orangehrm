<?php

namespace OrangeHRM\Entity\Decorator;

use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\Comment;
use OrangeHRM\Entity\Employee;

class CommentDecorator
{
    use EntityManagerHelperTrait;

    private Comment $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    public function setEmployeeById(?int $id): void
    {
        if (!$id) {
            return;
        }

        /** @var Employee $employee */
        $employee = $this->getReference(Employee::class, $id);

        $this->comment->setEmployee($employee);
    }
}