<?php

namespace OrangeHRM\Onboarding\Api;

use Carbon\Carbon;
use OrangeHRM\Admin\Traits\Service\TaskServiceTrait;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Exception\RecordNotFoundException;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Serializer\NormalizeException;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Api\V2\Validator\Rules\EntityUniquePropertyOption;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\Task;
use OrangeHRM\Onboarding\Api\Model\TaskDetailModel;
use OrangeHRM\Onboarding\Api\Model\TaskModel;
use OrangeHRM\Onboarding\Dto\TaskSearchFilterParams;

class TaskAPI extends Endpoint implements CrudEndpoint
{
    use TaskServiceTrait;

    public const PARAMETER_TITLE = 'title';
    public const PARAMETER_NOTES = 'notes';
    public const PARAMETER_TYPE = 'type';
    public const PARAMETER_JOB_TITLE_ID = 'jobTitleId';

    public const PARAM_RULE_TITLE_MAX_LENGTH = 255;
    public const PARAM_RULE_NOTES_MAX_LENGTH = 1000;

    public const FILTER_MODEL = 'model';
    public const MODEL_DEFAULT = 'default';
    public const MODEL_DETAILED = 'detailed';

    public const FILTER_TASK_TITLE = 'title';
    public const FILTER_TASK_TYPE = 'taskType';
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

        $title = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_QUERY,
            self::FILTER_TASK_TITLE
        );

        if (!is_null($title)) {
            $filterParams->setTitle($title);
        }

        $jobTitleId = $this->getRequestParams()->getIntOrNull(
            RequestParams::PARAM_TYPE_QUERY,
            self::FILTER_JOB_TITLE_ID
        );

        if (!is_null($jobTitleId)) {
            $filterParams->setJobTitleId($jobTitleId);
        }

        $taskType = $this->getRequestParams()->getIntOrNull(
            RequestParams::PARAM_TYPE_QUERY,
            self::FILTER_TASK_TYPE
        );

        if (!is_null($taskType)) {
            $filterParams->setType($taskType);
        }

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
            $this->getModelParamRule(),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_TASK_TITLE,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_FILTER_NAME_MAX_LENGTH]),
                ),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_JOB_TITLE_ID,
                    new Rule(Rules::POSITIVE),
                ),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_TASK_TYPE,
                    new Rule(Rules::INT_VAL),
                ),
            ),
            ...$this->getSortingAndPaginationParamsRules(TaskSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    public function create(): EndpointResourceResult
    {
        $task = $this->setParamsToTask();
        $jobTitleId = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_JOB_TITLE_ID);
        $task->getDecorator()->setJobTitleById($jobTitleId);

        $this->getTaskService()->saveTask($task);
        return new EndpointResourceResult(TaskModel::class, $task);

    }

    protected function getTitleRule(bool $update = false): ParamRule
    {
        $entityProperties = new EntityUniquePropertyOption();
        $ignoreValues = [];
        if ($update) {
            $ignoreValues['getId'] = $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_ATTRIBUTE,
                CommonParams::PARAMETER_ID
            );
        }
        $entityProperties->setIgnoreValues($ignoreValues);
        return new ParamRule(
            self::PARAMETER_TITLE,
            new Rule(Rules::STRING_TYPE),
            new Rule(Rules::LENGTH, [null, self::PARAM_RULE_TITLE_MAX_LENGTH]),
            new Rule(Rules::ENTITY_UNIQUE_PROPERTY, [Task::class, 'title', $entityProperties])
        );
    }

    private function setParamsToTask(): Task
    {
        $title = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_TITLE);
        $notes = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_NOTES);
        $taskType = $this->getRequestParams()->getIntOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_TYPE);

        $task = new Task();
        $task->setTitle($title);
        $task->setNotes($notes);
        $task->setType($taskType);
        $task->setCreatedAt(Carbon::now()->toDateTimeString());
        $task->setUpdatedAt(Carbon::now()->toDateTimeString());
        return $task;
    }

    private function getTypeRule(): ParamRule
    {
        return new ParamRule(
            self::PARAMETER_TYPE,
            new Rule(
                Rules::REQUIRED,
            ),
            new Rule(Rules::BETWEEN, [0, 1])
        );
    }

    private function getJobTitleIdRule(): ParamRule
    {
        return new ParamRule(
            self::PARAMETER_JOB_TITLE_ID,
            new Rule(Rules::POSITIVE),
        );
    }

    private function getNotesRule(): ParamRule
    {
        return new ParamRule(
            self::PARAMETER_NOTES,
            new Rule(
                Rules::REQUIRED,
            ),
            new Rule(Rules::LENGTH, [null, self::PARAM_RULE_NOTES_MAX_LENGTH]),
        );
    }

    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getTitleRule(),
            $this->getTypeRule(),
            $this->getNotesRule(),
            $this->getJobTitleIdRule(),
        );
    }

    /**
     * @throws NormalizeException
     * @throws DaoException
     */
    public function delete(): EndpointResult
    {
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS);
        $this->getTaskService()->deleteTask($ids);
        return new EndpointResourceResult(ArrayModel::class, $ids);
    }

    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_IDS),
        );
    }

    public function getOne(): EndpointResult
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $task = $this->getTaskService()->getTaskById($id);
        if (!$task instanceof Task) {
            throw new RecordNotFoundException();
        }

        return new EndpointResourceResult(TaskModel::class, $task);
    }

    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_ID),
        );
    }

    private function setTask(Task $task): void
    {
        $task->setTitle(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_TITLE
            )
        );
        $task->setType(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_TYPE
            )
        );
        $task->setNotes(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_NOTES
            )
        );
        $task->getDecorator()->setJobTitleById(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::FILTER_JOB_TITLE_ID
            )
        );
        $task->setUpdatedAt(Carbon::now()->toDateTimeString());
    }

    /**
     * @throws DaoException
     * @throws RecordNotFoundException
     * @throws NormalizeException
     */
    public function update(): EndpointResult
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);

        $task = $this->getTaskService()->getTaskById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($task, Task::class);
        $this->setTask($task);

        $this->getTaskService()->saveTask($task);

        return new EndpointResourceResult(TaskModel::class, $task);
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
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            ),
            $this->getTitleRule(true),
            $this->getTypeRule(),
            $this->getNotesRule(),
            $this->getJobTitleIdRule(),
        );
    }
}