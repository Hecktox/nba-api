<?php

namespace Vanier\Api\Exceptions;
use Slim\Exception\HttpSpecializedException;

class HttpInvalidBMIUnitException extends HttpSpecializedException
{
    protected $code = 400;
    protected $message = 'Invalid Unit';
    protected string $title = '404 Bad Request';
    protected string $description = 'The BMI unit you have selected is invalid.';
}
