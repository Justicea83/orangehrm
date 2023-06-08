<?php

namespace OrangeHRM\Onboarding\Api\Validation;

use Carbon\Carbon;
use OrangeHRM\Core\Api\V2\Exception\InvalidParamException;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Entity\GroupAssignment;
use OrangeHRM\Entity\Task;
use OrangeHRM\Entity\TaskAssignment;
use OrangeHRM\Entity\TaskGroup;
use OrangeHRM\Onboarding\Api\TaskAssignmentAPI;
use OrangeHRM\Core\Api\V2\RequestParams;

trait TaskAssignmentValidation
{
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
            $this->getTypeRule(),
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
            TaskAssignmentAPI::PARAMETER_DUE_DATE,
            new Rule(Rules::STRING_TYPE),
            new Rule(Rules::REQUIRED),
        );
    }

    protected function getStartDateRule(): ParamRule
    {
        return new ParamRule(
            TaskAssignmentAPI::PARAMETER_START_DATE,
            new Rule(Rules::STRING_TYPE),
            new Rule(Rules::REQUIRED),
        );
    }

    protected function getTypeRule(): ParamRule
    {
        return new ParamRule(
            TaskAssignmentAPI::PARAMETER_TYPE,
            new Rule(Rules::INT_TYPE),
            new Rule(Rules::REQUIRED),
        );
    }

    protected function getEndDateRule(): ParamRule
    {
        return new ParamRule(
            TaskAssignmentAPI::PARAMETER_END_DATE,
            new Rule(Rules::STRING_TYPE),
            new Rule(Rules::REQUIRED),
        );
    }

    protected function getEmployeeIdRule(): ParamRule
    {
        return new ParamRule(
            TaskAssignmentAPI::PARAMETER_EMPLOYEE_ID,
            new Rule(Rules::POSITIVE),
            new Rule(Rules::REQUIRED),
        );
    }

    protected function getSupervisorIdRule(): ParamRule
    {
        return new ParamRule(
            TaskAssignmentAPI::PARAMETER_SUPERVISOR_ID,
            new Rule(Rules::POSITIVE),
            new Rule(Rules::REQUIRED),
        );
    }

    protected function getNotesRule(): ParamRule
    {
        return new ParamRule(
            TaskAssignmentAPI::PARAMETER_NOTES,
            new Rule(Rules::STRING_TYPE),
        );
    }

    protected function getTasksRule(): ParamRule
    {
        return new ParamRule(
            TaskAssignmentAPI::PARAMETER_TASKS,
            new Rule(Rules::ARRAY_TYPE),
            new Rule(Rules::EACH, [
                new Rules\Composite\AllOf(
                    new Rule(
                        Rules::KEY,
                        [
                            TaskAssignmentAPI::PARAMETER_DUE_DATE,
                            new Rules\Composite\OneOf(new Rule(Rules::STRING_TYPE), new Rule(Rules::NOT_REQUIRED, [true]))
                        ]
                    ),
                    new Rule(
                        Rules::KEY,
                        [
                            TaskAssignmentAPI::PARAMETER_ID,
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
    protected function setParamsToTaskAssignment(): TaskAssignment
    {
        $dueDate = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, TaskAssignmentAPI::PARAMETER_DUE_DATE);
        $employeeId = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_BODY, TaskAssignmentAPI::PARAMETER_EMPLOYEE_ID);
        $endDate = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, TaskAssignmentAPI::PARAMETER_END_DATE);
        $type = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_BODY, TaskAssignmentAPI::PARAMETER_TYPE);
        $notes = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, TaskAssignmentAPI::PARAMETER_NOTES);
        $startDate = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, TaskAssignmentAPI::PARAMETER_START_DATE);
        $supervisorId = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_BODY, TaskAssignmentAPI::PARAMETER_SUPERVISOR_ID);
        $tasks = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, TaskAssignmentAPI::PARAMETER_TASKS);

        $taskAssignment = new TaskAssignment();
        $now = Carbon::now()->toDateTimeString();

        $taskAssignment->setNotes($notes);
        $taskAssignment->setType($type);
        $taskAssignment->setName($this->getTaskAssignmentName($type));
        $taskAssignment->setCreatedAt($now);
        $taskAssignment->setUpdatedAt($now);

        foreach ($tasks as $task) {
            $taskGroup = new TaskGroup();
            $taskGroup->setDueDate($task['dueDate']);
            $taskGroup->getDecorator()->setTaskById($task['id']);
            $taskGroup->setTaskAssignment($taskAssignment);

            $taskAssignment->getTaskGroups()->add($taskGroup);
        }

        $taskGroupAssignment = new GroupAssignment();
        $taskGroupAssignment->setDueDate($dueDate);
        $taskGroupAssignment->setEndDate($endDate);
        $taskGroupAssignment->setStartDate($startDate);
        $taskGroupAssignment->getDecorator()->setEmployeeById($employeeId);
        $taskGroupAssignment->getDecorator()->setSupervisorById($supervisorId);
        $taskGroupAssignment->setTaskAssignment($taskAssignment);
        $taskGroupAssignment->setCreatedAt($now);
        $taskGroupAssignment->setUpdatedAt($now);

        $taskAssignment->getGroupAssignments()->add($taskGroupAssignment);
        return $taskAssignment;
    }

    private function getTaskAssignmentName(int $type): string
    {
        $name = match ($type) {
            Task::TYPE_ONBOARDING => 'Onboarding',
            Task::TYPE_OFFBOARDING => 'Offboarding',
        };

        return sprintf('%s %s', $name, 'Tasks');
    }
}