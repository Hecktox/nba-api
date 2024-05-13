<?php

namespace Vanier\Api\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Vanier\Api\Helpers\webServiceInvokerHelper;

class TvMazeController extends BaseController
{
    public function searchNbaShows(Request $request, Response $response, array $uri_args)
    {
        $ws_invoker = new webServiceInvokerHelper();
        $uri = "https://api.tvmaze.com/search/shows?q=nba";
        $shows = $ws_invoker->parseShows($uri);
        $data["shows"] = $shows;
    
        return $this->makeResponse($response, $shows);
    }
}
