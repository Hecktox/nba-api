<?php

namespace Vanier\Api\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Vanier\Api\Models\TeamModel;
use Vanier\Api\Exceptions\HttpInvalidInputException;

class TeamController extends BaseController
{

    private $team_model = null;
    public function __construct()
    {
        $this->team_model = new TeamModel;
    }
    public function handleGetAllTeams(Request $request, Response $response, array $uri_args): Response
    {
        $filters = $request->getQueryParams();

        $this->team_model->setPaginationOptions(
            $filters["page"] ?? 4,
            $filters["page_size"] ?? 10,
        );
        $teams = $this->team_model->getAllTeams($filters);

        return $this->makeResponse($response, $teams);
    }
    public function handleGetTeamId(Request $request, Response $response, array $uri_args): Response
    {
        $team_id = $uri_args["team_id"];
        $team_info = $this->team_model->getTeamInfo($team_id);

        if (empty ($team_info)) {
            throw new HttpInvalidInputException(
                $request,
                "The supplied team id is not valid"
            );
        }

        return $this->makeResponse($response, $team_info);
    }
}
