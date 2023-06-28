<?php

namespace OrangeHRM\Comments\Api;

use OrangeHRM\Comments\Api\Model\CommentModel;
use OrangeHRM\Comments\Api\Validation\CommentValidation;
use OrangeHRM\Comments\Traits\Service\CommentServiceTrait;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Exception\InvalidParamException;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Serializer\NormalizeException;
use OrangeHRM\Core\Exception\DaoException;

class CommentAPI extends Endpoint implements CrudEndpoint
{
    use CommentValidation, CommentServiceTrait;

    public const PARAMETER_BODY = 'body';
    public const PARAMETER_MODEL_TYPE = 'model_type';
    public const PARAMETER_MODEL_ID = 'model_id';
    public const PARAMETER_PARENT_ID = 'parent_id';


    public function getAll(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @throws InvalidParamException|NormalizeException
     */
    public function create(): EndpointResourceResult
    {
        $task = $this->setCreateParamsToComment();

        $this->getCommentService()->saveComment($task);

        return new EndpointResourceResult(CommentModel::class, $task);
    }

    /**
     * @throws NormalizeException
     * @throws DaoException
     */
    public function delete(): EndpointResult
    {
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS);
        $this->getCommentService()->deleteCommentById($ids);
        return new EndpointResourceResult(ArrayModel::class, $ids);
    }

    public function getOne(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @throws NormalizeException
     * @throws DaoException
     * @throws InvalidParamException
     */
    public function update(): EndpointResourceResult
    {
        $task = $this->setUpdateParamsToComment();
        $this->getCommentService()->saveComment($task);
        return new EndpointResourceResult(CommentModel::class, $task);
    }

}