<?php

namespace Vanier\Api\Exceptions;
use Slim\Exception\HttpSpecializedException;

class HttpNoContentException extends HttpSpecializedException
{
    protected $code = 204;
    protected $message = "No Content";
    protected string $title = "204 No Content";
    protected string $description = "No content has been found for the specified request.";
}
