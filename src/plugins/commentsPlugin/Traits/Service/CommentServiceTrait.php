<?php

namespace OrangeHRM\Comments\Traits\Service;

use OrangeHRM\Comments\Service\CommentService;
use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Framework\Services;

trait CommentServiceTrait
{
    use ServiceContainerTrait;

    public function getCommentService(): CommentService
    {
        return $this->getContainer()->get(Services::COMMENT_SERVICE);
    }
}