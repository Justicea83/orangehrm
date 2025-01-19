<?php

namespace OrangeHRM\Core\Api\V2\Exception;

use Exception;
use Throwable;

class InvalidLicenseException extends Exception
{
    public const DEFAULT_ERROR_MESSAGE = "Licenses exhausted, contact support!";

    public function __construct($message = self::DEFAULT_ERROR_MESSAGE, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}