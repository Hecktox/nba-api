<?php

namespace Vanier\Api\Exceptions;
use Slim\Exception\HttpSpecializedException;

class HttpInvalidPaginationParameterException extends HttpSpecializedException
{
    protected $code = 400;

    protected $message = "Bad Request";

    protected string $title = "400 Bad Request";

    protected string $description = "Invalid pagination parameter detected. Please ensure proper datatype and syntax.";
}
