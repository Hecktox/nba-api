<?php

namespace Vanier\Api\Middleware;

use Exception;
use LogicException;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpUnauthorizedException;
use UnexpectedValueException;
use Firebase\JWT\JWT;
use Vanier\Api\Controllers\LoggingController;
use Vanier\Api\Helpers\JWTManager;

class JWTAuthMiddleware implements MiddlewareInterface
{
    public function __construct(array $options = [])
    {
    }

    public function process(Request $request, RequestHandler $handler): ResponseInterface
    {
        /*
         * 1) Routes to ignore (public routes):
         *    We need to ignore the routes that enable client applications
         *    to create an account and request a JWT token.
         */
        $publicRoutes = ['/nba-api/v1/account', '/nba-api/v1/token'];

        if ($request->getMethod() === 'POST' && in_array($request->getUri()->getPath(), $publicRoutes)) {
            return $handler->handle($request);
        }

        /*
         * 2) Retrieve the token from the request Authorization header.
         */
        $authorizationHeader = $request->getHeaderLine('Authorization');
        if (!$authorizationHeader) {
            try {
                throw new HttpForbiddenException($request, 'Missing authorization token');
            } catch (Exception $ex){
                LoggingController::logError($ex->getMessage());
            }
            
            
        }

        /*
         * 3) Parse the token: remove the "Bearer " word.
         */
        $token = str_replace('Bearer ', '', $authorizationHeader);

        try {
            /*
             * 4) Try to decode the JWT token.
             */
            $decodedToken = JWTManager::decodeJWT($token, JWTManager::SIGNATURE_ALGO);
        } catch (Exception $ex) {
            LoggingController::logError($ex->getMessage());
            throw new HttpForbiddenException($request, 'Invalid authorization token');
        }

        /*
         * 5) Access to POST, PUT, and DELETE operations must be restricted:
         *    Only admin accounts can be authorized.
         */
        if (in_array($request->getMethod(), ['POST', 'PUT', 'DELETE']) && isset($decodedToken['role']) && $decodedToken['role'] !== 'admin') {
            try {
                throw new HttpForbiddenException($request, 'Insufficient permission');
            } catch (Exception $ex){
                LoggingController::logError($ex->getMessage());
            }
            
        }

        /*
         * 6) The client application has been authorized:
         *    Now we need to store the token payload in the request object. 
         *    The payload is needed for logging purposes and needs to be 
         *    passed as an attribute to the request's handling callbacks.  
         *    This will allow the target resource's callback to access the 
         *    token payload for various purposes (such as logging, etc.). 
         *    Use the APP_JWT_TOKEN_KEY as the attribute name.
         */
        // Store the token payload in the request object for logging purposes
        $request = $request->withAttribute('APP_JWT_TOKEN_KEY', $decodedToken);

        /*
         * 7) At this point, the client app's request has been authorized:
         *    We pass the request to the next middleware in the middleware stack.
         */
        return $handler->handle($request);
    }
}
