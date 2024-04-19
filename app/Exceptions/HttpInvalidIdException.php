<?php

namespace Vanier\Api\Exceptions;
use Slim\Exception\HttpSpecializedException;

class HttpInvalidIdException extends HttpSpecializedException
{
    protected $code = 404;
    protected $message = 'Not Found';
    protected string $title = '404 Not Found';
    protected string $description = 'Resource with the provided ID does not exist!';
}
