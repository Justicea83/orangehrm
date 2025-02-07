<?php

namespace OrangeHRM\ZkTeco\Api;

use OrangeHRM\Core\Api\V2\CollectionEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Entity\ZkTecoConfig;
use OrangeHRM\ZkTeco\Api\Traits\ZkServiceTrait;
use OrangeHRM\ZkTeco\Api\Traits\ZkTecoCommonParamRuleCollection;

class ZkTecoTestConnectionAPI extends Endpoint implements CollectionEndpoint
{
    use ZkServiceTrait, ZkTecoCommonParamRuleCollection;

    public function getAll(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    public function create(): EndpointResult
    {
        $zkTecoConfig = new ZkTecoConfig();

        $zkTecoConfig->setHost(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                ZkTecoAPI::PARAMETER_HOSTNAME
            ),
        );
        $zkTecoConfig->setPort(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                ZkTecoAPI::PARAMETER_PORT
            ),
        );
        $zkTecoConfig->setAdminUsername(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                ZkTecoAPI::PARAMETER_ADMIN_USERNAME
            ),
        );
        $zkTecoConfig->setAdminPassword(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                ZkTecoAPI::PARAMETER_ADMIN_PASSWORD
            ),
        );

        $zkTecoConfig->setScheme(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                ZkTecoAPI::PARAMETER_SCHEME
            ),
        );

        return new EndpointResourceResult(ArrayModel::class, $this->getZkTecoService()->testConnection($zkTecoConfig));
    }

    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        $paramRules = $this->getParamRuleCollection();
        $paramRules->removeParamValidation(ZkTecoAPI::PARAMETER_ENABLED);
        $paramRules->removeParamValidation(ZkTecoAPI::PARAMETER_SYNC_INTERVAL);
        $paramRules->removeParamValidation(ZkTecoAPI::PARAMETER_OVERRIDE_SALARY);
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