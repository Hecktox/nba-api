<?php

namespace Vanier\Api\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Vanier\Api\Controllers\LoggingController;
use Vanier\Api\Helpers\DateTimeHelper;
use Vanier\Api\Models\AccessLogModel;

class LoggingMiddleware implements MiddlewareInterface
{
    private $account_log_model = null;

    public function __construct(){
        $this->account_log_model = new AccessLogModel();
    }


    public function process(Request $request, RequestHandler $handler): ResponseInterface
    {
        // 2) We now can log some access info:
        $client_ip = $_SERVER["REMOTE_ADDR"];
        $method = $request->getMethod();
        $uri = $request->getUri()->getPath();
        $log_record = $client_ip . ' ' . $method . ' ' . $uri . ' ' . "Access log:";


        //Local log
        $extras = $request->getQueryParams();
        LoggingController::logAccess($log_record, $extras);

        //DB log (a bit weird since I had to find a way to verify who the currently logged in user is, thought of using the JWT token but that was going to be pretty crazy to use as a query param for the ws_users table, so i settled for using the body of the request, you have to pass in your email and your JWT token in the headers, = db logging should work for get methods only)
        
        //$rBody = $request->getParsedBody();

        // $user_id["id"] = $rBody["id"];
        // $user_email["email"] = $rBody["email"];
        // $user_info = [$user_id["id"], $user_email["email"]];

        //$this->account_log_model->createLogEntry($user_info, $method);
        

        $response = $handler->handle($request);
        return $response;
    } 
}
