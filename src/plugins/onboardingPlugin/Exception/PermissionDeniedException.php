<?php

namespace OrangeHRM\Onboarding\Exception;

use Exception;
use Throwable;

class PermissionDeniedException extends Exception
{
    public const DEFAULT_ERROR_MESSAGE = "Permission Denied";

    public function __construct($message = self::DEFAULT_ERROR_MESSAGE, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}