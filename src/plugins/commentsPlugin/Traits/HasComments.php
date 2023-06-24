<?php

namespace OrangeHRM\Comments\Traits;

use OrangeHRM\Comments\Exception\ClassNotFoundException;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\Comment;
use OrangeHRM\ORM\ListSorter;
use ReflectionClass;
use ReflectionException;
use Throwable;

trait HasComments
{
    use AuthUserTrait, EntityManagerHelperTrait;

    public function getComments(): array
    {
        try {
            $modelType = $this->getCallingClassName();
            $modelId = $this->getId();

            $q = $this->createQueryBuilder(Comment::class, 'c');

            $q->andWhere('c.modelType = :modelType')
                ->setParameter('modelType', $modelType);

            $q->andWhere('c.modelId = :modelId')
                ->setParameter('modelId', $modelId);

            $q->andWhere('c.empNumber = :empNumber')
                ->setParameter('empNumber', $this->getAuthUser()->getEmpNumber());

            $q->andWhere(
                $q->expr()->isNull('c.parentId')
            );

            $q->orderBy('c.id', ListSorter::DESCENDING);

            /** @var Array<Comment> $results */
            $results = $q->getQuery()->execute();

            return array_map(function (Comment $comment) {
                return [
                    'body' => $comment->getBody(),
                    'id' => $comment->getId(),
                    'user' => [
                        'id' => $comment->getEmployee()->getEmpNumber(),
                        'name' => $comment->getEmployee()->getFullName(),
                    ]
                ];
            }, $results);

        } catch (Throwable) {
            return [];
        }
    }

    /**
     * @throws ReflectionException|ClassNotFoundException
     */
    private function getCallingClassName(): string
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
        if (isset($trace[1]['object'])) {
            $class = get_class($trace[1]['object']);
            $reflection = new ReflectionClass($class);
            return $reflection->getName();
        }
        throw new ClassNotFoundException();
    }
}