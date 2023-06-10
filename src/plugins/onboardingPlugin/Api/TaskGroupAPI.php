<?php

namespace OrangeHRM\Onboarding\Api;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Onboarding\Api\Model\TaskGroupDetailModel;
use OrangeHRM\Onboarding\Api\Validation\TaskGroupValidation;
use OrangeHRM\Onboarding\Dto\TaskGroupSearchFilterParams;
use OrangeHRM\Onboarding\Traits\Service\TaskGroupServiceTrait;

class TaskGroupAPI extends Endpoint implements CrudEndpoint
{
    use TaskGroupValidation, TaskGroupServiceTrait;

    public const FILTER_GROUP_ASSIGNMENT = 'groupAssignmentId';


    public function getAll(): EndpointResult
    {
        $groupAssignment = $this->getRequestParams()->getIntOrNull(
            RequestParams::PARAM_TYPE_QUERY,
            self::FILTER_GROUP_ASSIGNMENT
        );
        $filterParams = TaskGroupSearchFilterParams::instance()
            ->setGroupAssignmentId($groupAssignment);

        $tasks = $this->getTaskGroupService()->getTaskGroupList($filterParams);
        $count = $this->getTaskGroupService()->getTaskGroupListCount($filterParams);
        return new EndpointCollectionResult(
            TaskGroupDetailModel::class,
            $tasks,
            new ParameterBag([
                CommonParams::PARAMETER_TOTAL => $count,
                CommonParams::PARAMETER_LIMIT => $filterParams->getLimit(),
                CommonParams::PARAMETER_OFFSET => $filterParams->getOffset()
            ])
        );
    }

    public function create(): EndpointResult
    {
        // TODO: Implement create() method.
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