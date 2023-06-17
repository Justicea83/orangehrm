<?php

namespace OrangeHRM\Onboarding\Api;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CollectionEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Onboarding\Api\Model\GroupAssignmentModel;
use OrangeHRM\Onboarding\Api\Validation\MyAssignmentsValidation;
use OrangeHRM\Onboarding\Dto\GroupAssignmentSearchFilterParams;
use OrangeHRM\Onboarding\Traits\Service\GroupAssignmentServiceTrait;

class EmployeeAssignmentsAPI extends Endpoint implements CollectionEndpoint
{
    use MyAssignmentsValidation, GroupAssignmentServiceTrait;

    public function getAll(): EndpointResult
    {
        $filterParams = GroupAssignmentSearchFilterParams::instance();
        $this->setSortingAndPaginationParams($filterParams);

        $assignments = $this->getGroupAssignmentService()->getEmployeeAssignments($filterParams);
        $count = $this->getGroupAssignmentService()->getEmployeeAssignmentsCount($filterParams);
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

    public function create(): EndpointResult
    {
        // TODO: Implement create() method.
    }


    public function delete(): EndpointResult
    {
        // TODO: Implement delete() method.
    }

}