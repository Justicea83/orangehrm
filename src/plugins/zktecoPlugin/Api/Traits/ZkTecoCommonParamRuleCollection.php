<?php

namespace OrangeHRM\ZkTeco\Api\Traits;

use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\ZkTeco\Api\ZkTecoAPI;
use OrangeHRM\ZkTeco\Api\ZkTecoSalaryAPI;
use OrangeHRM\ZkTeco\Dao\PunchPairFilterParams;
use OrangeHRM\Framework\Http\Request;

trait ZkTecoCommonParamRuleCollection
{
    protected function getParamRuleCollection(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                ZkTecoAPI::PARAMETER_HOSTNAME,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, ZkTecoAPI::PARAMETER_RULE_ALL_MAX_LENGTH])
            ),
            new ParamRule(
                ZkTecoAPI::PARAMETER_SCHEME,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [4, 5])
            ),
            new ParamRule(
                ZkTecoAPI::PARAMETER_PORT,
                new Rule(Rules::NUMBER),
            ),
            new ParamRule(
                ZkTecoAPI::PARAMETER_ADMIN_PASSWORD,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, ZkTecoAPI::PARAMETER_RULE_ALL_MAX_LENGTH])
            ),
            new ParamRule(
                ZkTecoAPI::PARAMETER_ADMIN_USERNAME,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, ZkTecoAPI::PARAMETER_RULE_ALL_MAX_LENGTH])
            ),
            new ParamRule(
                ZkTecoAPI::PARAMETER_SYNC_INTERVAL,
                new Rule(Rules::INT_VAL),
                new Rule(Rules::BETWEEN, [1, 23]),
            ),
            new ParamRule(
                ZkTecoAPI::PARAMETER_ENABLED,
                new Rule(Rules::BOOL_VAL)
            ),
            new ParamRule(
                ZkTecoAPI::PARAMETER_OVERRIDE_SALARY,
                new Rule(Rules::BOOL_VAL)
            ),
        );
    }

    private function getSalaryParamRuleCollection(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    ZkTecoSalaryAPI::PARAMETER_PAY_GRADE_ID,
                    new Rule(Rules::POSITIVE)
                )
            ),
            new ParamRule(
                ZkTecoSalaryAPI::PARAMETER_SALARY_COMPONENT,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, ZkTecoAPI::PARAMETER_RULE_ALL_MAX_LENGTH])
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    ZkTecoSalaryAPI::PARAMETER_PAY_FREQUENCY_ID,
                    new Rule(Rules::POSITIVE)
                )
            ),
            new ParamRule(
                ZkTecoSalaryAPI::PARAMETER_CURRENCY_ID,
                new Rule(Rules::CURRENCY)
            ),
            new ParamRule(
                ZkTecoSalaryAPI::PARAMETER_SALARY_AMOUNT,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, ZkTecoAPI::PARAMETER_RULE_ALL_MAX_LENGTH])
            ),
        );
    }

    private function setPunchPairReportParams(): PunchPairFilterParams
    {
        return PunchPairFilterParams::instance()
            ->setDate(
                $this->getRequestParams()->getStringOrNull(
                    RequestParams::PARAM_TYPE_QUERY,
                    PunchPairFilterParams::PARAMETER_DATE
                )
            )
            ->setExportType(
                $this->getRequestParams()->getStringOrNull(
                    RequestParams::PARAM_TYPE_QUERY,
                    PunchPairFilterParams::PARAMETER_EXPORT_TYPE
                )
            )
            ->setDepartments(
                array_filter(
                    explode(',', $this->getRequestParams()->getStringOrNull(
                        RequestParams::PARAM_TYPE_QUERY,
                        PunchPairFilterParams::PARAMETER_DEPARTMENTS
                    ) ?? ''),
                    fn($value) => $value !== ''
                )
            )
            ->setEmployees(
                array_filter(
                    explode(',', $this->getRequestParams()->getStringOrNull(
                        RequestParams::PARAM_TYPE_QUERY,
                        PunchPairFilterParams::PARAMETER_EMPLOYEES
                    ) ?? ''),
                    fn($value) => $value !== ''
                )
            )
            ->setJobTitles(
                array_filter(
                    explode(',', $this->getRequestParams()->getStringOrNull(
                        RequestParams::PARAM_TYPE_QUERY,
                        PunchPairFilterParams::PARAMETER_JOB_TITLES
                    ) ?? ''),
                    fn($value) => $value !== ''
                )
            )
            ->setExportColumns(
                array_filter(
                    explode(',', $this->getRequestParams()->getStringOrNull(
                        RequestParams::PARAM_TYPE_QUERY,
                        PunchPairFilterParams::PARAMETER_EXPORT_COLUMNS
                    ) ?? ''),
                    fn($value) => $value !== ''
                )
            )
            ->setReportMode(
                $this->getRequestParams()->getStringOrNull(
                    RequestParams::PARAM_TYPE_QUERY,
                    PunchPairFilterParams::PARAMETER_REPORT_MODE
                )
            );
    }

    private function setPunchPairExportReportParams(Request $request): PunchPairFilterParams
    {
        return PunchPairFilterParams::instance()
            ->setDate(
                $request->query->get(PunchPairFilterParams::PARAMETER_DATE)
            )
            ->setExportType(
                $request->query->get(PunchPairFilterParams::PARAMETER_EXPORT_TYPE)
            )
            ->setDepartments(
                array_filter(
                    explode(',', $request->query->get(PunchPairFilterParams::PARAMETER_DEPARTMENTS) ?? ''),
                    fn($value) => $value !== ''
                )
            )
            ->setEmployees(
                array_filter(
                    explode(',', $request->query->get(PunchPairFilterParams::PARAMETER_EMPLOYEES) ?? ''),
                    fn($value) => $value !== ''
                )
            )
            ->setJobTitles(
                array_filter(
                    explode(',', $request->query->get(PunchPairFilterParams::PARAMETER_JOB_TITLES) ?? ''),
                    fn($value) => $value !== ''
                )
            )
            ->setExportColumns(
                array_filter(
                    explode(',', $request->query->get(PunchPairFilterParams::PARAMETER_EXPORT_COLUMNS) ?? ''),
                    fn($value) => $value !== ''
                )
            )
            ->setReportMode(
                $request->query->get(PunchPairFilterParams::PARAMETER_REPORT_MODE)
            );
    }
}