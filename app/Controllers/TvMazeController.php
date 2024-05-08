<?php

namespace Vanier\Api\Controllers;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class TvMazeController extends BaseController
{
    public function searchNbaShows(Request $request, Response $response, array $uri_args)
    {
        
        $queryParams = $request->getQueryParams();
    
        $tvShow = $queryParams['c'] ?? ''; 
        
        
        $client = new Client([
            
            'base_uri' => 'https://api.tvmaze.com/',
            
            'timeout' => 2.0,
        ]);

        
        $apiResponse = $client->get("shows/14769");

        
        if ($apiResponse->getStatusCode() !== 200) {
            return $response->withStatus(500)->write(json_encode(['error' => 'Failed to retrieve data from the API']));
        }

        
        $apiData = json_decode($apiResponse->getBody()->getContents(), true);

    
        $response->getBody()->write(json_encode($apiData));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
