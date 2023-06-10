<?php

namespace OrangeHRM\Onboarding\Api\Validation;

use Carbon\Carbon;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\Exception\InvalidParamException;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Entity\GroupAssignment;
use OrangeHRM\Entity\TaskGroup;
use OrangeHRM\Onboarding\Api\GroupAssignmentAPI;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Onboarding\Dto\GroupAssignmentSearchFilterParams;
use OrangeHRM\Onboarding\Traits\Service\TaskGroupServiceTrait;

trait GroupAssignmentValidation
{
    use AuthUserTrait, TaskGroupServiceTrait;

    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            ...$this->getSortingAndPaginationParamsRules(GroupAssignmentSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
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
            $this->getTypesRule(),
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
        throw $this->getNotImplementedException();
    }

    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            ),
            $this->getDueDateRule(),
            $this->getStartDateRule(),
            $this->getEmployeeIdRule(),
            $this->getNotesRule(),
            $this->getTasksRule(),
            $this->getSupervisorIdRule(),
            $this->getEndDateRule(),
            $this->getNameRule(),
            $this->getTypesRule(),
        );
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

    protected function getTypesRule(): ParamRule
    {
        return new ParamRule(
            GroupAssignmentAPI::PARAMETER_TYPES,
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
        $types = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, GroupAssignmentAPI::PARAMETER_TYPES);

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
        $groupAssignment->setTypes($types);
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

    /**
     * @throws InvalidParamException
     * @throws DaoException
     */
    protected function setGroupAssignment(GroupAssignment $groupAssignment): void
    {
        $dueDate = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, GroupAssignmentAPI::PARAMETER_DUE_DATE);
        $employeeId = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_BODY, GroupAssignmentAPI::PARAMETER_EMPLOYEE_ID);
        $endDate = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, GroupAssignmentAPI::PARAMETER_END_DATE);
        $name = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, GroupAssignmentAPI::PARAMETER_NAME);
        $notes = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, GroupAssignmentAPI::PARAMETER_NOTES);
        $startDate = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, GroupAssignmentAPI::PARAMETER_START_DATE);
        $supervisorId = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_BODY, GroupAssignmentAPI::PARAMETER_SUPERVISOR_ID);
        $tasks = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, GroupAssignmentAPI::PARAMETER_TASKS);
        $types = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, GroupAssignmentAPI::PARAMETER_TYPES);

        $now = Carbon::now()->toDateTimeString();

        $groupAssignment->setDueDate($dueDate);
        $groupAssignment->setEndDate($endDate);
        $groupAssignment->setStartDate($startDate);
        $groupAssignment->getDecorator()->setEmployeeById($employeeId);
        $groupAssignment->getDecorator()->setSupervisorById($supervisorId);
        $groupAssignment->setName($name);
        $groupAssignment->setNotes($notes);
        $groupAssignment->setTypes($types);
        $groupAssignment->setUpdatedAt($now);

        $ids = array_map(fn(TaskGroup $taskGroup) => $taskGroup->getId(), $groupAssignment->getTaskGroups()->toArray());
        $this->getTaskGroupService()->deleteTaskGroup($ids);

        $groupAssignment->getTaskGroups()->clear();

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
    }
}