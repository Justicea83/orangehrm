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
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Serializer\NormalizeException;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\GroupAssignment;
use OrangeHRM\Onboarding\Api\Model\GroupAssignmentModel;
use OrangeHRM\Onboarding\Api\Validation\GroupAssignmentValidation;
use OrangeHRM\Onboarding\Dto\GroupAssignmentSearchFilterParams;
use OrangeHRM\Onboarding\Traits\Service\GroupAssignmentServiceTrait;

class GroupAssignmentAPI extends Endpoint implements CrudEndpoint
{
    use GroupAssignmentValidation, GroupAssignmentServiceTrait;

    public const PARAMETER_DUE_DATE = 'dueDate';
    public const PARAMETER_EMPLOYEE_ID = 'employeeId';
    public const PARAMETER_END_DATE = 'endDate';
    public const PARAMETER_ID = 'id';
    public const PARAMETER_NAME = 'name';
    public const PARAMETER_TYPES = 'types';
    public const PARAMETER_NOTES = 'notes';
    public const PARAMETER_START_DATE = 'startDate';
    public const PARAMETER_SUPERVISOR_ID = 'supervisorId';
    public const PARAMETER_TASKS = 'tasks';

    public const MODEL_DEFAULT = 'default';
    public const MODEL_DETAILED = 'detailed';

    public const MODEL_MAP = [
        self::MODEL_DEFAULT => GroupAssignmentModel::class,
        self::MODEL_DETAILED => GroupAssignmentModel::class,
    ];

    public function getAll(): EndpointResult
    {
        $filterParams = GroupAssignmentSearchFilterParams::instance();
        $this->setSortingAndPaginationParams($filterParams);

        $assignments = $this->getGroupAssignmentService()->getGroupAssignments($filterParams);
        $count = $this->getGroupAssignmentService()->getGroupAssignmentsCount($filterParams);
        return new EndpointCollectionResult(
            GroupAssignmentModel::class,
            $assignments,
            new ParameterBag([
                CommonParams::PARAMETER_TOTAL => $count,
                CommonParams::PARAMETER_LIMIT => $filterParams->getLimit(),
                CommonParams::PARAMETER_OFFSET => $filterParams->getOffset()
            ])
        );
    }

    /**
     * @throws InvalidParamException
     * @throws NormalizeException
     */
    public function create(): EndpointResult
    {
        $groupAssignment = $this->setParamsToGroupAssignment();
        $this->getGroupAssignmentService()->saveGroupAssignment($groupAssignment);

        return new EndpointResourceResult(GroupAssignmentModel::class, $groupAssignment);
    }

    /**
     * @throws DaoException
     * @throws NormalizeException
     */
    public function delete(): EndpointResult
    {
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS);
        $this->getGroupAssignmentService()->deleteGroupAssignmentById($ids);
        return new EndpointResourceResult(ArrayModel::class, $ids);
    }

    public function getOne(): EndpointResult
    {
       throw $this->getNotImplementedException();
    }

    /**
     * @throws NormalizeException
     * @throws InvalidParamException
     * @throws RecordNotFoundException
     * @throws DaoException
     */
    public function update(): EndpointResult
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);

        $groupAssignment = $this->getGroupAssignmentService()->getGroupAssignmentById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($groupAssignment, GroupAssignment::class);
        $this->setGroupAssignment($groupAssignment);

        $this->getGroupAssignmentService()->saveGroupAssignment($groupAssignment);

        return new EndpointResourceResult(GroupAssignmentModel::class, $groupAssignment);
    }
}