<?php

namespace OrangeHRM\Onboarding\Api\Validation;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Api\V2\Validator\Rules\EntityUniquePropertyOption;
use OrangeHRM\Entity\TaskType;
use OrangeHRM\Onboarding\Api\TaskTypeAPI;

trait TaskTypeValidation
{
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection();
    }

    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getNameRule()
        );
    }

    private function setParamsToTaskType(): TaskType
    {
        $name = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, TaskTypeAPI::PARAMETER_NAME);

        $task = new TaskType();
        $task->setName($name);
        return $task;
    }

    protected function getNameRule(bool $update = false): ParamRule
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
            TaskTypeAPI::PARAMETER_NAME,
            new Rule(Rules::STRING_TYPE),
            new Rule(Rules::REQUIRED),
            new Rule(Rules::ENTITY_UNIQUE_PROPERTY, [TaskType::class, 'name', $entityProperties])
        );
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
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            ),
            $this->getNameRule(true),
        );
    }
}