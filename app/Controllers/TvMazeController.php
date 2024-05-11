<?php

namespace Vanier\Api\Controllers;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class TvMazeController extends BaseController
{
    public function searchNbaShows(Request $request, Response $response, array $uri_args)
    {
        // NBA shows search query
        $tvShow = 'nba';

        $client = new Client([
            'base_uri' => 'https://api.tvmaze.com/',
            'timeout' => 2.0,
        ]);

        $apiResponse = $client->get("search/shows?q=$tvShow");

        if ($apiResponse->getStatusCode() !== 200) {
            $errorResponse = ['error' => 'Failed to retrieve data from the API'];
            return $this->makeResponse($response, $errorResponse, 500);
        }

        $apiData = json_decode($apiResponse->getBody()->getContents(), true);

        return $this->makeResponse($response, $apiData, 200);
    }
}
