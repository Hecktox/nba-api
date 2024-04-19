<?php

namespace Vanier\Api\Exceptions;
use Slim\Exception\HttpSpecializedException;

class HttpInvalidSyntaxException extends HttpSpecializedException
{
    protected $code = 400;
    protected $message = 'Bad Request';
    protected string $title = '400 Bad Request';
    protected string $description = 'Invalid syntax provided!';
}
