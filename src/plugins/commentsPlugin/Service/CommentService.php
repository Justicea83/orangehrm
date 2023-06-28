<?php

namespace OrangeHRM\Comments\Service;

use OrangeHRM\Comments\Dao\CommentDao;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\Comment;

class CommentService
{
    protected ?CommentDao $commentDao = null;

    public function getCommentDao(): CommentDao
    {
        if (is_null($this->commentDao)) {
            $this->commentDao = new CommentDao();
        }
        return $this->commentDao;
    }

    public function saveComment(Comment $comment): Comment
    {
        return $this->getCommentDao()->saveComment($comment);
    }

    /**
     * @throws DaoException
     */
    public function getCommentById(int $id): ?Comment
    {
        return $this->getCommentDao()->getCommentById($id);
    }

    /**
     * @throws DaoException
     */
    public function deleteCommentById(array $ids): int
    {
        return $this->getCommentDao()->deleteCommentById($ids);
    }
}