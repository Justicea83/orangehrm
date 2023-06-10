<?php

namespace OrangeHRM\Onboarding\Api\Validation;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Onboarding\Api\TaskGroupAPI;
use OrangeHRM\Onboarding\Dto\TaskGroupSearchFilterParams;

trait TaskGroupValidation
{
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    TaskGroupAPI::FILTER_GROUP_ASSIGNMENT,
                    new Rule(Rules::INT_VAL),
                ),
            ),
            ...$this->getSortingAndPaginationParamsRules(TaskGroupSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        // TODO: Implement getValidationRuleForCreate() method.
    }

    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_IDS),
        );
    }

    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        // TODO: Implement getValidationRuleForGetOne() method.
    }

    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        // TODO: Implement getValidationRuleForUpdate() method.
    }
}