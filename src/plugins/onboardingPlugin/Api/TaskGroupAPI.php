<?php

namespace OrangeHRM\Onboarding\Api;

use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Onboarding\Api\Validation\TaskGroupValidation;

class TaskGroupAPI extends Endpoint implements CrudEndpoint
{
    use TaskGroupValidation;

    public function getAll(): EndpointResult
    {
        // TODO: Implement getAll() method.
    }

    public function create(): EndpointResult
    {
        // TODO: Implement create() method.
    }

    public function delete(): EndpointResult
    {
        // TODO: Implement delete() method.
    }

    public function getOne(): EndpointResult
    {
        // TODO: Implement getOne() method.
    }

    public function update(): EndpointResult
    {
        // TODO: Implement update() method.
    }
}