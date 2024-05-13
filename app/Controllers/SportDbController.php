<?php

namespace Vanier\Api\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Vanier\Api\Helpers\webServiceInvokerHelper;


class SportDbController extends BaseController
{

    public function searchLeagues(Request $request, Response $response, array $uri_args): Response
    {
        $queryParams = $request->getQueryParams();
        $country = $queryParams['c'] ?? '';

        $ws_invoker = new webServiceInvokerHelper();
        $uri = "https://www.thesportsdb.com/api/v1/json/3/search_all_leagues.php?s=Basketball&c=$country";
        $leagues = $ws_invoker->parseSports($uri);
        $player["leagues"] = $leagues;

        return $this->makeResponse($response, $leagues);
    }
}
