<?php

namespace Vanier\Api\Controllers;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class SportDbController extends BaseController
{

    public function searchLeagues(Request $request, Response $response, array $uri_args)
    {
        
        $queryParams = $request->getQueryParams();
    
        $country = $queryParams['c'] ?? ''; 
        
        
        $client = new Client([
            
            'base_uri' => 'https://www.thesportsdb.com/api/v1/json/3/',
            
            'timeout' => 2.0,
        ]);

        
        $apiResponse = $client->get("search_all_leagues.php?s=Basketball&c=$country");

        
        if ($apiResponse->getStatusCode() !== 200) {
            return $response->withStatus(500)->write(json_encode(['error' => 'Failed to retrieve data from the API']));
        }

        
        $apiData = json_decode($apiResponse->getBody()->getContents(), true);

    
        $response->getBody()->write(json_encode($apiData));
        return $response->withHeader('Content-Type', 'application/json');
    }

}
