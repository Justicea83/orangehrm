<?php

namespace OrangeHRM\Comments\Exception;

use Exception;
use Throwable;

class ClassNotFoundException extends Exception
{
    public const DEFAULT_ERROR_MESSAGE = "Class Not Found";

    public function __construct($message = self::DEFAULT_ERROR_MESSAGE, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}