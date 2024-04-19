<?php

namespace Vanier\Api\Exceptions;
use Slim\Exception\HttpSpecializedException;

class HttpForeignKeyException extends HttpSpecializedException
{
    protected $code = 404;
    protected $message = 'Not Found';
    protected string $title = '404 Not Found';
    protected string $description = 'Foreign key invalid (in other table)!';
}
