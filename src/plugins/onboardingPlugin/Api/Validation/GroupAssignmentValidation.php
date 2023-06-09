<?php

namespace OrangeHRM\Onboarding\Api\Validation;

use Carbon\Carbon;
use OrangeHRM\Core\Api\V2\Exception\InvalidParamException;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Entity\GroupAssignment;
use OrangeHRM\Entity\TaskGroup;
use OrangeHRM\Onboarding\Api\GroupAssignmentAPI;
use OrangeHRM\Core\Api\V2\RequestParams;

trait GroupAssignmentValidation
{
    use AuthUserTrait;

    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        // TODO: Implement getValidationRuleForGetAll() method.
    }

    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getDueDateRule(),
            $this->getStartDateRule(),
            $this->getEmployeeIdRule(),
            $this->getNotesRule(),
            $this->getTasksRule(),
            $this->getSupervisorIdRule(),
            $this->getEndDateRule(),
            $this->getNameRule(),
        );
    }

    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        // TODO: Implement getValidationRuleForDelete() method.
    }

    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        // TODO: Implement getValidationRuleForGetOne() method.
    }

    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        // TODO: Implement getValidationRuleForUpdate() method.
    }

    protected function getDueDateRule(): ParamRule
    {
        return new ParamRule(
            GroupAssignmentAPI::PARAMETER_DUE_DATE,
            new Rule(Rules::STRING_TYPE),
            new Rule(Rules::REQUIRED),
        );
    }

    protected function getStartDateRule(): ParamRule
    {
        return new ParamRule(
            GroupAssignmentAPI::PARAMETER_START_DATE,
            new Rule(Rules::STRING_TYPE),
            new Rule(Rules::REQUIRED),
        );
    }

    protected function getEndDateRule(): ParamRule
    {
        return new ParamRule(
            GroupAssignmentAPI::PARAMETER_END_DATE,
            new Rule(Rules::STRING_TYPE),
            new Rule(Rules::REQUIRED),
        );
    }

    protected function getNameRule(): ParamRule
    {
        return new ParamRule(
            GroupAssignmentAPI::PARAMETER_NAME,
            new Rule(Rules::STRING_TYPE),
            new Rule(Rules::REQUIRED),
        );
    }

    protected function getEmployeeIdRule(): ParamRule
    {
        return new ParamRule(
            GroupAssignmentAPI::PARAMETER_EMPLOYEE_ID,
            new Rule(Rules::POSITIVE),
            new Rule(Rules::REQUIRED),
        );
    }

    protected function getSupervisorIdRule(): ParamRule
    {
        return new ParamRule(
            GroupAssignmentAPI::PARAMETER_SUPERVISOR_ID,
            new Rule(Rules::POSITIVE),
            new Rule(Rules::REQUIRED),
        );
    }

    protected function getNotesRule(): ParamRule
    {
        return new ParamRule(
            GroupAssignmentAPI::PARAMETER_NOTES,
            new Rule(Rules::STRING_TYPE),
        );
    }

    protected function getTasksRule(): ParamRule
    {
        return new ParamRule(
            GroupAssignmentAPI::PARAMETER_TASKS,
            new Rule(Rules::ARRAY_TYPE),
            new Rule(Rules::EACH, [
                new Rules\Composite\AllOf(
                    new Rule(
                        Rules::KEY,
                        [
                            GroupAssignmentAPI::PARAMETER_DUE_DATE,
                            new Rules\Composite\OneOf(new Rule(Rules::STRING_TYPE), new Rule(Rules::NOT_REQUIRED, [true]))
                        ]
                    ),
                    new Rule(
                        Rules::KEY,
                        [
                            GroupAssignmentAPI::PARAMETER_ID,
                            new Rules\Composite\AllOf(new Rule(Rules::POSITIVE))
                        ]
                    ),
                )
            ]),
        );
    }

    /**
     * @throws InvalidParamException
     */
    protected function setParamsToGroupAssignment(): GroupAssignment
    {
        $dueDate = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, GroupAssignmentAPI::PARAMETER_DUE_DATE);
        $employeeId = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_BODY, GroupAssignmentAPI::PARAMETER_EMPLOYEE_ID);
        $endDate = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, GroupAssignmentAPI::PARAMETER_END_DATE);
        $name = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, GroupAssignmentAPI::PARAMETER_NAME);
        $notes = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, GroupAssignmentAPI::PARAMETER_NOTES);
        $startDate = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, GroupAssignmentAPI::PARAMETER_START_DATE);
        $supervisorId = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_BODY, GroupAssignmentAPI::PARAMETER_SUPERVISOR_ID);
        $tasks = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, GroupAssignmentAPI::PARAMETER_TASKS);

        $now = Carbon::now()->toDateTimeString();

        $groupAssignment = new GroupAssignment();
        $groupAssignment->setDueDate($dueDate);
        $groupAssignment->setEndDate($endDate);
        $groupAssignment->setStartDate($startDate);
        $groupAssignment->getDecorator()->setEmployeeById($employeeId);
        $groupAssignment->getDecorator()->setSupervisorById($supervisorId);
        $groupAssignment->setCreatedAt($now);
        $groupAssignment->setName($name);
        $groupAssignment->setNotes($notes);
        $groupAssignment->setUpdatedAt($now);
        $groupAssignment->setCreatorById($this->getAuthUser()->getEmpNumber());

        $priority = 1;
        foreach ($tasks as $task) {
            $taskGroup = new TaskGroup();
            $taskGroup->setDueDate($task['dueDate']);
            $taskGroup->getDecorator()->setTaskById($task['id']);
            $taskGroup->setPriority($priority);
            $taskGroup->setGroupAssignment($groupAssignment);
            $groupAssignment->getTaskGroups()->add($taskGroup);

            $priority++;
        }


        return $groupAssignment;
    }
}