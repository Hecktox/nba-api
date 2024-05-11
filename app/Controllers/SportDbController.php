<?php

namespace Vanier\Api\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Vanier\Api\Models\SportsDbModel;

use GuzzleHttp\Client;

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
            $errorResponse = ['error' => 'Failed to retrieve data from the API'];
            return $this->makeResponse($response, $errorResponse, 500);
        }

        $apiData = json_decode($apiResponse->getBody()->getContents(), true);
        return $this->makeResponse($response, $apiData, 200);
    }
}
