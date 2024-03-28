<?php declare(strict_types=1);

namespace Vanier\Api\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Vanier\Api\Exceptions\HttpNotAcceptableException;

class ContentNegotiationMiddleware implements MiddlewareInterface
{
   /**
    *   Throws a new HttpNotAcceptableException if the requested 
    *   resource representation by the client is not 'application/json'.
    *
    *   @param string $requestRepresentation Comma separated string containing the "Accept" header values of the request
    *   @param  \Slim\Psr7\Response $response Request response
    *   @param array $data Array containing the response values for the request in case it is invalid
    *   @param string $payload String containing the JSON representation of the supplied value
    *
    */
    public function process(Request $request, RequestHandler $handler): ResponseInterface
    {
        // echo "Hello! From test middleware!";

        $requestRepresentation = $request->getHeaderLine("Accept");

        if(str_contains($requestRepresentation, 'application/json')) {

        } else {
            //throw new HttpNotAcceptableException($request);
            $response = new \Slim\Psr7\Response();  

            $data = [
            'message'=>'Not Accepted',
            'description'=>'Resource representation not supported.'];

            $payload = json_encode($data);

            $response->getBody()->write($payload);
            return $response
                ->withStatus(406);
        }


        //! DO NOT remove or change the following statements. 
        $response = $handler->handle($request);
        return $response;
    }    
}
