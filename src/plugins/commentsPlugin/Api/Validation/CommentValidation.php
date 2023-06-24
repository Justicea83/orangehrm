<?php

namespace OrangeHRM\Comments\Api\Validation;

use Carbon\Carbon;
use OrangeHRM\Comments\Api\CommentAPI;
use OrangeHRM\Comments\Traits\Service\CommentServiceTrait;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\Exception\InvalidParamException;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Entity\Comment;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Entity\GroupAssignment;

trait CommentValidation
{
    use CommentServiceTrait, AuthUserTrait;

    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_IDS),
        );
    }

    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @throws InvalidParamException
     * @throws DaoException
     */
    protected function setUpdateParamsToComment(): Comment{
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);

        $body = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, CommentAPI::PARAMETER_BODY);
        $modelId = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_BODY, CommentAPI::PARAMETER_MODEL_ID);
        $modelType = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, CommentAPI::PARAMETER_MODEL_TYPE);
        $parentId = $this->getRequestParams()->getIntOrNull(RequestParams::PARAM_TYPE_BODY, CommentAPI::PARAMETER_PARENT_ID);

        $comment = $this->getCommentService()->getCommentById($id);
        $comment->setBody($body);
        $comment->setModelId($modelId);
        $comment->setModelType($this->getCommentableFqd($modelType));
        $comment->setParentId($parentId);
        $comment->setUpdatedAt(Carbon::now()->toDateTimeString());

        return $comment;
    }
    /**
     * @throws InvalidParamException
     */
    protected function setCreateParamsToComment(): Comment
    {
        $body = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, CommentAPI::PARAMETER_BODY);
        $modelId = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_BODY, CommentAPI::PARAMETER_MODEL_ID);
        $modelType = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, CommentAPI::PARAMETER_MODEL_TYPE);
        $parentId = $this->getRequestParams()->getIntOrNull(RequestParams::PARAM_TYPE_BODY, CommentAPI::PARAMETER_PARENT_ID);

        $now = Carbon::now()->toDateTimeString();

        $comment = new Comment();
        $comment->setBody($body);
        $comment->setModelType($this->getCommentableFqd($modelType));
        $comment->setModelId($modelId);
        $comment->setParentId($parentId);
        $comment->setCreatedAt($now);
        $comment->setUpdatedAt($now);
        $comment->getDecorator()->setEmployeeById($this->getAuthUser()->getEmpNumber());

        return $comment;
    }

    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            ),
            ...$this->commentRules()
        );
    }

    private function commentRules(): array
    {
        return [
            $this->getBodyRule(),
            $this->getModelIdRule(),
            $this->getModelTypeRule(),
            $this->getParentIdRule(),
        ];
    }

    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            ...$this->commentRules(),
        );
    }

    public function getBodyRule(): ParamRule
    {
        return new ParamRule(
            CommentAPI::PARAMETER_BODY,
            new Rule(Rules::REQUIRED),
            new Rule(Rules::STRING_TYPE)
        );
    }

    public function getModelIdRule(): ParamRule
    {
        return new ParamRule(
            CommentAPI::PARAMETER_MODEL_ID,
            new Rule(Rules::REQUIRED),
            new Rule(Rules::INT_TYPE)
        );
    }

    public function getModelTypeRule(): ParamRule
    {
        return new ParamRule(
            CommentAPI::PARAMETER_MODEL_TYPE,
            new Rule(Rules::REQUIRED),
            new Rule(Rules::STRING_TYPE),
            new Rule(Rules::IN, [array_keys($this->commentableModels())])
        );
    }

    public function getParentIdRule(): ParamRule
    {
        return $this->getValidationDecorator()->notRequiredParamRule(
            new ParamRule(
                CommentAPI::PARAMETER_PARENT_ID,
                new Rule(Rules::INT_TYPE)
            )
        );
    }

    public function commentableModels(): array
    {
        return [
            'groupAssignment' => GroupAssignment::class
        ];
    }

    public function getCommentableFqd(string $name): string
    {
        return $this->commentableModels()[$name];
    }
}