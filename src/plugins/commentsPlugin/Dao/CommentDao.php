<?php

namespace OrangeHRM\Comments\Dao;

use Exception;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\Comment;

class CommentDao extends BaseDao
{
    public function saveComment(Comment $comment): Comment
    {
        $this->persist($comment);
        return $comment;
    }

    /**
     * @throws DaoException
     */
    public function getCommentById(int $id): ?Comment
    {
        try {
            $comment = $this->getRepository(Comment::class)->find($id);
            if ($comment instanceof Comment) {
                return $comment;
            }
            return null;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @throws DaoException
     */
    public function deleteCommentById(array $ids): int
    {
        try {
            $q = $this->createQueryBuilder(Comment::class, 'c');
            $q->delete()
                ->where($q->expr()->in('c.id', ':ids'))
                ->setParameter('ids', $ids);
            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }
}