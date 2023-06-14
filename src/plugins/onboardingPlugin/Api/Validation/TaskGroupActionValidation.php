<?php

namespace OrangeHRM\Onboarding\Api\Validation;

use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Onboarding\Api\TaskGroupActionAPI;
use OrangeHRM\ORM\ListSorter;

trait TaskGroupActionValidation
{
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        // TODO: Implement getValidationRuleForGetOne() method.
    }

    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getActionRule(),
            $this->getTaskGroupIdRule(),
            $this->getGroupAssignmentIdRule(),
        );
    }

    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        // TODO: Implement getValidationRuleForDelete() method.
    }

    protected function getActionRule(): ParamRule
    {
        return new ParamRule(
            TaskGroupActionAPI::PARAMETER_ACTION,
            new Rule(Rules::STRING_TYPE),
            new Rule(Rules::REQUIRED),
            new Rule(Rules::IN, [TaskGroupActionAPI::ALLOWED_ACTIONS]),
        );
    }

    protected function getTaskGroupIdRule(): ParamRule
    {
        return new ParamRule(
            TaskGroupActionAPI::PARAMETER_TASK_GROUP_ID,
            new Rule(
                Rules::ONE_OF,
                [
                    new Rule(Rules::POSITIVE),
                    new Rule(Rules::NOT_REQUIRED),
                ]
            )
        );
    }

    protected function getGroupAssignmentIdRule(): ParamRule
    {
        return new ParamRule(
            TaskGroupActionAPI::PARAMETER_GROUP_ASSIGNMENT_ID,
            new Rule(Rules::POSITIVE),
            new Rule(Rules::REQUIRED),
        );
    }
}