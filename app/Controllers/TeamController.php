<?php

namespace Vanier\Api\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Vanier\Api\Models\TeamModel;
use Vanier\Api\Exceptions\HttpInvalidInputExecption;
class TeamController extends BaseController
{

    private $team_model = null;
    public function __construct()
    {
        $this->team_model= new TeamModel;
    }
    public function handleGetAllTeams(Request $request, Response $response, array $uri_args): Response
    {
        //we get the values to after pass it to the model and do pagination
        $filters = $request->getQueryParams();

        //TODO: validate the paginaton params
        $this->team_model->setPaginationOptions(
            $filters["page"] ?? 4,
            $filters["page_size"] ?? 10,
        );
        $teams = $this->team_model->getAllTeams($filters);

        return $this->makeResponse($response, $teams);
    }
}
