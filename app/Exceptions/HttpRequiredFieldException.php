<?php

namespace Vanier\Api\Exceptions;
use Slim\Exception\HttpSpecializedException;

class HttpRequiredFieldException extends HttpSpecializedException
{
    protected $code = 400;
    protected $message = 'Bad Request';
    protected string $title = '400 Bad Request';
    protected string $description = 'Required field(s) are missing or empty!';
}
