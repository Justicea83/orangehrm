<?php

namespace OrangeHRM\ZkTeco\Api;

use OrangeHRM\Core\Api\V2\CollectionEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\ZkTeco\Api\Traits\ZkServiceTrait;
use OrangeHRM\ZkTeco\Model\ZkTecoConfigModel;

class ZkTecoUserSyncAPI extends Endpoint implements CollectionEndpoint
{
    use ZkServiceTrait;

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
        return new EndpointResourceResult(ZkTecoConfigModel::class, $this->getZkTecoService()->triggerForceSync());
    }

    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection();
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