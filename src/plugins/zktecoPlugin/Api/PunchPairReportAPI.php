<?php

namespace OrangeHRM\ZkTeco\Api;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CollectionEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\ZkTeco\Api\Traits\ZkServiceTrait;
use OrangeHRM\ZkTeco\Api\Traits\ZkTecoCommonParamRuleCollection;
use OrangeHRM\ZkTeco\Dao\PunchPairFilterParams;

class PunchPairReportAPI extends Endpoint implements CollectionEndpoint
{
    use ZkServiceTrait, ZkTecoCommonParamRuleCollection;
    public function getAll(): EndpointResult
    {
        $filterParams = $this->setPunchPairReportParams();
        $this->setSortingAndPaginationParams($filterParams);

        $results = $this->getZkTecoService()->fetchTransactions($filterParams);

        return new EndpointCollectionResult(
            ArrayModel::class,
            $results->getData(),
            new ParameterBag([
                CommonParams::PARAMETER_TOTAL => $results->getCount(),
            ])
        );
    }

    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    PunchPairFilterParams::PARAMETER_DATE,
                    new Rule(Rules::STRING_TYPE),
                ),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    PunchPairFilterParams::PARAMETER_EMPLOYEES,
                    new Rule(Rules::STRING_TYPE),
                ),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    PunchPairFilterParams::PARAMETER_DEPARTMENTS,
                    new Rule(Rules::STRING_TYPE),
                ),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    PunchPairFilterParams::PARAMETER_JOB_TITLES,
                    new Rule(Rules::STRING_TYPE),
                ),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    PunchPairFilterParams::PARAMETER_REPORT_MODE,
                    new Rule(Rules::STRING_TYPE),
                ),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    PunchPairFilterParams::PARAMETER_EXPORT_TYPE,
                    new Rule(Rules::STRING_TYPE),
                ),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    PunchPairFilterParams::PARAMETER_EXPORT_COLUMNS,
                    new Rule(Rules::STRING_TYPE),
                ),
            ),
            ...$this->getSortingAndPaginationParamsRules(PunchPairFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    public function create(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    public function delete(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }
}