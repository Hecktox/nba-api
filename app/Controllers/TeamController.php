<?php

namespace Vanier\Api\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Vanier\Api\Models\TeamModel;
use Vanier\Api\Exceptions\HttpInvalidInputException;
use Vanier\Api\Exceptions\HttpInvalidPaginationParameterException;

class TeamController extends BaseController
{

    private $team_model = null;
    public function __construct()
    {
        $this->team_model = new TeamModel;
    }
    private function assertTeamId($request, $team_id)
    {
        if (!is_numeric($team_id) || strlen($team_id) !== 10) {
            throw new HttpInvalidInputException($request, "Invalid game id format. Must be a 10-digit number.");
        }
    }
    public function handleGetAllTeams(Request $request, Response $response, array $uri_args): Response
    {
        $filters = $request->getQueryParams();
        $page = $filters["page"] ?? 1;
        $page_size = $filters["page_size"] ?? 10;

        if (!is_numeric($page) || $page < 1) {
            throw new HttpInvalidPaginationParameterException($request, "Invalid page number. Must be a positive integer.");
        }
        if (!is_numeric($page_size) || $page_size < 1 || $page_size > 50) {
            throw new HttpInvalidPaginationParameterException($request, "Invalid page size. Must be an integer between 1 and 50.");
        }

        $this->team_model->setPaginationOptions($page, $page_size);
        $teams = $this->team_model->getAllTeams($filters);

        return $this->makeResponse($response, $teams);
    }

    public function handleGetTeamId(Request $request, Response $response, array $uri_args): Response
    {
        $team_id = $uri_args["team_id"];
    
        // Fetch team information
        $team_info = $this->team_model->getTeamInfo($team_id);
    
        // Check if team information is empty
        if (empty($team_info)) {
            throw new HttpInvalidInputException(
                $request,
                "The supplied team ID is not valid"
            );
        }
    
        return $this->makeResponse($response, $team_info);
    }
    
    public function handleGetTeamHistory(Request $request, Response $response, array $uri_args): Response
    {
        $team_id = $uri_args["team_id"];
        $team_info = $this->team_model->getTeamHistory($team_id);

        $payload = json_encode($team_info);
        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }

}
