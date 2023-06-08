<?php

namespace OrangeHRM\Onboarding\Api;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Exception\InvalidParamException;
use OrangeHRM\Core\Api\V2\Exception\RecordNotFoundException;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Serializer\NormalizeException;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\Task;
use OrangeHRM\Entity\TaskType;
use OrangeHRM\Onboarding\Api\Model\TaskModel;
use OrangeHRM\Onboarding\Api\Model\TaskTypeModel;
use OrangeHRM\Onboarding\Api\Validation\TaskTypeValidation;
use OrangeHRM\Onboarding\Traits\Service\TaskTypeServiceTrait;

class TaskTypeAPI extends Endpoint implements CrudEndpoint
{
    use TaskTypeServiceTrait, TaskTypeValidation;

    public const PARAMETER_NAME = 'name';


    public function getAll(): EndpointResult
    {
        return new EndpointCollectionResult(
            TaskTypeModel::class,
            $this->getTaskTypeService()->getTaskTypeList()
        );
    }

    public function create(): EndpointResult
    {
        $taskType = $this->setParamsToTaskType();
        $this->getTaskTypeService()->saveTaskType($taskType);

        return new EndpointResourceResult(TaskTypeModel::class, $taskType);
    }

    /**
     * @throws DaoException
     * @throws NormalizeException
     */
    public function delete(): EndpointResult
    {
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS);
        $this->getTaskTypeService()->deleteTaskType($ids);

        return new EndpointResourceResult(ArrayModel::class, $ids);
    }

    public function getOne(): EndpointResult
    {
    }

    /**
     * @throws InvalidParamException
     * @throws DaoException
     * @throws RecordNotFoundException
     * @throws NormalizeException
     */
    public function update(): EndpointResult
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);

        $taskType = $this->getTaskTypeService()->getTaskTypeById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($taskType, TaskType::class);

        $taskType->setName(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_NAME
            )
        );
        $this->getTaskTypeService()->saveTaskType($taskType);

        return new EndpointResourceResult(TaskTypeModel::class, $taskType);
    }
}