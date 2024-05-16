<?php

namespace Vanier\Api\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Vanier\Api\Controllers\LoggingController;
use Vanier\Api\Helpers\DateTimeHelper;

class LoggingMiddleware implements MiddlewareInterface
{


    public function process(Request $request, RequestHandler $handler): ResponseInterface
    {

        // 2) We now can log some access info:
        $client_ip = $_SERVER["REMOTE_ADDR"];
        $method = $request->getMethod();
        $uri = $request->getUri()->getPath();
        $log_record = $client_ip . ' ' . $method . ' ' . $uri . ' ' . "Access log:";

        // 3) Prepare any extra info.
        $extras = $request->getQueryParams();

        LoggingController::logAccess($log_record, $extras);

        // $now = DateTimeHelper::getDateAndTime(DateTimeHelper::D_M_Y);

        // $response->getBody()->write("Web Service accessed at: " . $now . "Action performed: $method" . "On resource: " . $uri);

        $response = $handler->handle($request);
        return $response;
    } 
}
