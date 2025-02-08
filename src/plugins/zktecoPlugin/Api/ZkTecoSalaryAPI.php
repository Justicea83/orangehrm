<?php

namespace OrangeHRM\ZkTeco\Api;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Serializer\NormalizeException;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Api\V2\Validator\ValidatorException;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Pim\Api\Model\EmployeeSalaryModel;
use OrangeHRM\ZkTeco\Api\Traits\ZkServiceTrait;
use OrangeHRM\ZkTeco\Api\Traits\ZkTecoCommonParamRuleCollection;

class ZkTecoSalaryAPI extends Endpoint implements CrudEndpoint
{
    use ZkServiceTrait, ZkTecoCommonParamRuleCollection;

    public const PARAMETER_PAY_GRADE_ID = 'payGradeId';
    public const PARAMETER_SALARY_COMPONENT = 'salaryComponent';
    public const PARAMETER_PAY_FREQUENCY_ID = 'payFrequencyId';
    public const PARAMETER_CURRENCY_ID = 'currencyId';
    public const PARAMETER_SALARY_AMOUNT = 'salaryAmount';

    public function getAll(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @throws DaoException
     * @throws NormalizeException
     */
    public function create(): EndpointResult
    {
        $salaryData = [
            self::PARAMETER_PAY_GRADE_ID => $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_PAY_GRADE_ID
            ),
            self::PARAMETER_SALARY_COMPONENT => $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_SALARY_COMPONENT
            ),
            self::PARAMETER_PAY_FREQUENCY_ID => $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_PAY_FREQUENCY_ID
            ),
            self::PARAMETER_CURRENCY_ID => $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_CURRENCY_ID
            ),
            self::PARAMETER_SALARY_AMOUNT => $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_SALARY_AMOUNT
            ),
        ];

        $this->getZkTecoService()->saveSalary($salaryData);

        return new EndpointResourceResult(
            ArrayModel::class,
            [],
        );
    }

    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return $this->getSalaryParamRuleCollection();
    }

    /**
     * @throws NormalizeException
     * @throws DaoException
     */
    public function delete(): EndpointResult
    {
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS);
        $this->getZkTecoService()->deleteSalariesByIds($ids);
        return new EndpointResourceResult(
            ArrayModel::class,
            $ids,
        );
    }

    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_IDS),
        );
    }

    public function getOne(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @throws NormalizeException
     * @throws DaoException
     */
    public function update(): EndpointResult
    {
        $id = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_BODY,
            CommonParams::PARAMETER_ID
        );

        $salaryData = [
            self::PARAMETER_PAY_GRADE_ID => $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_PAY_GRADE_ID
            ),
            self::PARAMETER_SALARY_COMPONENT => $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_SALARY_COMPONENT
            ),
            self::PARAMETER_PAY_FREQUENCY_ID => $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_PAY_FREQUENCY_ID
            ),
            self::PARAMETER_CURRENCY_ID => $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_CURRENCY_ID
            ),
            self::PARAMETER_SALARY_AMOUNT => $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_SALARY_AMOUNT
            ),
        ];

        $this->getZkTecoService()->editSalaryById($id, $salaryData);

        return new EndpointResourceResult(
            ArrayModel::class,
            [],
        );
    }

    /**
     * @throws ValidatorException
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        $paramRules = $this->getSalaryParamRuleCollection();
        $paramRules->addParamValidation(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::STRING_TYPE)
            )
        );
        return $paramRules;
    }
}