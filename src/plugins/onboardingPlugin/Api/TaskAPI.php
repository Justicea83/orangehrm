<?php

namespace OrangeHRM\Onboarding\Api;

use OrangeHRM\Admin\Traits\Service\TaskServiceTrait;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Onboarding\Api\Model\TaskDetailModel;
use OrangeHRM\Onboarding\Api\Model\TaskModel;
use OrangeHRM\Onboarding\Dto\TaskSearchFilterParams;

class TaskAPI extends Endpoint implements CrudEndpoint
{
    use TaskServiceTrait;

    public const FILTER_MODEL = 'model';
    public const MODEL_DEFAULT = 'default';
    public const MODEL_DETAILED = 'detailed';

    public const FILTER_TASK_TITLE = 'title';
    public const FILTER_TASK_TYPE = 'type';
    public const FILTER_JOB_TITLE_ID = 'jobTitleId';

    public const PARAM_RULE_EMP_PICTURE_FILE_NAME_MAX_LENGTH = 100;
    public const PARAM_RULE_FILTER_NAME_MAX_LENGTH = 100;
    public const PARAM_RULE_FILTER_NAME_OR_ID_MAX_LENGTH = 100;

    public const MODEL_MAP = [
        self::MODEL_DEFAULT => TaskModel::class,
        self::MODEL_DETAILED => TaskDetailModel::class,
    ];

    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        $model = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_QUERY,
            self::FILTER_MODEL,
            self::MODEL_DEFAULT
        );
        return self::MODEL_MAP[$model];
    }

    public function getAll(): EndpointResult
    {
        $filterParams = new TaskSearchFilterParams();
        $this->setSortingAndPaginationParams($filterParams);

        // TODO set search filters

        $tasks = $this->getTaskService()->getTaskList($filterParams);
        $count = $this->getTaskService()->getTaskListCount($filterParams);
        return new EndpointCollectionResult(
            $this->getModelClass(),
            $tasks,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])
        );
    }

    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_TASK_TITLE,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_FILTER_NAME_MAX_LENGTH]),
                ),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_TASK_TYPE,
                    new Rule(Rules::INT_TYPE),
                ),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_JOB_TITLE_ID,
                    new Rule(Rules::POSITIVE),
                ),
            ),
            $this->getModelParamRule(),
            ...$this->getSortingAndPaginationParamsRules(TaskSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    public function create(): EndpointResult
    {
        // TODO: Implement create() method.
    }

    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        // TODO: Implement getValidationRuleForCreate() method.
    }

    public function delete(): EndpointResult
    {
        // TODO: Implement delete() method.
    }

    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        // TODO: Implement getValidationRuleForDelete() method.
    }

    public function getOne(): EndpointResult
    {
        // TODO: Implement getOne() method.
    }

    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        // TODO: Implement getValidationRuleForGetOne() method.
    }

    public function update(): EndpointResult
    {
        // TODO: Implement update() method.
    }

    protected function getModelParamRule(): ParamRule
    {
        return $this->getValidationDecorator()->notRequiredParamRule(
            new ParamRule(
                self::FILTER_MODEL,
                new Rule(Rules::IN, [array_keys(self::MODEL_MAP)])
            )
        );
    }

    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        // TODO: Implement getValidationRuleForUpdate() method.
    }
}