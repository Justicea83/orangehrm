<?php

namespace OrangeHRM\Onboarding\Api\Validation;

use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Onboarding\Dto\GroupAssignmentSearchFilterParams;

trait MyAssignmentsValidation
{
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            ...$this->getSortingAndPaginationParamsRules(GroupAssignmentSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }
}