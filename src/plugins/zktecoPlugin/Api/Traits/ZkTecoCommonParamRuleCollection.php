<?php

namespace OrangeHRM\ZkTeco\Api\Traits;

use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\ZkTeco\Api\ZkTecoAPI;

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
}