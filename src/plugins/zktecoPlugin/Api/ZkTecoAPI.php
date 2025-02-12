<?php

namespace OrangeHRM\ZkTeco\Api;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\ResourceEndpoint;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Entity\ZkTecoConfig;
use OrangeHRM\ZkTeco\Api\Traits\ZkServiceTrait;
use OrangeHRM\ZkTeco\Api\Traits\ZkTecoCommonParamRuleCollection;
use OrangeHRM\ZkTeco\Model\ZkTecoConfigModel;

class ZkTecoAPI extends Endpoint implements ResourceEndpoint
{
    use ZkServiceTrait, ZkTecoCommonParamRuleCollection;

    public const PARAMETER_ENABLED = 'enable';
    public const PARAMETER_HOSTNAME = 'host';
    public const PARAMETER_PORT = 'port';
    public const PARAMETER_SCHEME = 'scheme';
    public const PARAMETER_OVERRIDE_SALARY = 'overrideSalary';
    public const PARAMETER_ADMIN_USERNAME = 'adminUsername';
    public const PARAMETER_ADMIN_PASSWORD = 'adminPassword';
    public const PARAMETER_SYNC_INTERVAL = 'syncInterval';


    public const PARAMETER_RULE_ALL_MAX_LENGTH = 255;

    public function getOne(): EndpointResult
    {
        $config = $this->getZkTecoService()->getConfig();
        return new EndpointResourceResult(ZkTecoConfigModel::class, $config);
    }

    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        $paramRules = new ParamRuleCollection();
        $paramRules->addExcludedParamKey(CommonParams::PARAMETER_ID);
        return $paramRules;
    }

    public function update(): EndpointResult
    {
        $zkTecoConfig = new ZkTecoConfig();
        $zkTecoConfig->setEnabled(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_ENABLED
            ),
        );
        $zkTecoConfig->setHost(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_HOSTNAME
            ),
        );
        $zkTecoConfig->setPort(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_PORT
            ),
        );
        $zkTecoConfig->setAdminUsername(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_ADMIN_USERNAME
            ),
        );
        $zkTecoConfig->setAdminPassword(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_ADMIN_PASSWORD
            ),
        );
        $zkTecoConfig->setSyncInterval(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_SYNC_INTERVAL
            ),
        );
        $zkTecoConfig->setOverrideSalary(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_OVERRIDE_SALARY
            ),
        );
        $zkTecoConfig->setScheme(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_SCHEME
            ),
        );

        $results = $this->getZkTecoService()->saveConfig($zkTecoConfig);
        return new EndpointResourceResult(ZkTecoConfigModel::class, $results);
    }

    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        $paramRules = $this->getParamRuleCollection();
        $paramRules->addExcludedParamKey(CommonParams::PARAMETER_ID);
        return $paramRules;
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