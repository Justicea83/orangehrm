<?php

namespace OrangeHRM\Onboarding\Api;

use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Exception\InvalidParamException;
use OrangeHRM\Core\Api\V2\Serializer\NormalizeException;
use OrangeHRM\Onboarding\Api\Model\TaskAssignmentModel;
use OrangeHRM\Onboarding\Api\Model\TaskModel;
use OrangeHRM\Onboarding\Api\Validation\TaskAssignmentValidation;
use OrangeHRM\Onboarding\Traits\Service\TaskAssignmentServiceTrait;

class TaskAssignmentAPI extends Endpoint implements CrudEndpoint
{
    use TaskAssignmentValidation, TaskAssignmentServiceTrait;

    public const PARAMETER_DUE_DATE = 'dueDate';
    public const PARAMETER_EMPLOYEE_ID = 'employeeId';
    public const PARAMETER_END_DATE = 'endDate';
    public const PARAMETER_ID = 'id';
    public const PARAMETER_TYPE = 'type';
    public const PARAMETER_NOTES = 'notes';
    public const PARAMETER_START_DATE = 'startDate';
    public const PARAMETER_SUPERVISOR_ID = 'supervisorId';
    public const PARAMETER_TASKS = 'tasks';

    public const MODEL_DEFAULT = 'default';
    public const MODEL_DETAILED = 'detailed';

    public const MODEL_MAP = [
        self::MODEL_DEFAULT => TaskAssignmentModel::class,
        self::MODEL_DETAILED => TaskAssignmentModel::class,
    ];

    public function getAll(): EndpointResult
    {
        // TODO: Implement getAll() method.
    }


    /**
     * @throws InvalidParamException
     * @throws NormalizeException
     */
    public function create(): EndpointResult
    {
        $taskAssignment = $this->setParamsToTaskAssignment();
        //dd($taskAssignment);
        $this->getTaskAssignmentService()->saveTaskAssignment($taskAssignment);

        return new EndpointResourceResult(TaskAssignmentModel::class, $taskAssignment);
    }



    public function delete(): EndpointResult
    {
        // TODO: Implement delete() method.
    }



    public function getOne(): EndpointResult
    {
        // TODO: Implement getOne() method.
    }



    public function update(): EndpointResult
    {
        // TODO: Implement update() method.
    }


}