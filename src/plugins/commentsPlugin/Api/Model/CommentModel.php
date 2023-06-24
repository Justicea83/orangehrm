<?php

namespace OrangeHRM\Comments\Api\Model;


use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Core\Api\V2\Serializer\ModelTrait;
use OrangeHRM\Entity\Comment;

class CommentModel implements Normalizable
{
    use ModelTrait;

    public function __construct(Comment $comment)
    {
        $this->setEntity($comment);
        $this->setFilters([
            'body',
            'id',
            ['getEmployee', 'getEmpNumber'],
            ['getEmployee', 'getFullName'],
        ]);

        $this->setAttributeNames(
            [
                'body',
                'id',
                ['user', 'id'],
                ['user', 'name'],
            ]
        );
    }
}