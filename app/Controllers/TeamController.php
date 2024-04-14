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
        if (strlen($team_id) !== 10) {
            throw new HttpInvalidInputException($request, "Invalid team ID format. Must be a 10-character string.");
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
    public function handleCreateTeam(Request $request, Response $response, array $uri_args): Response
    {
        $teams = $request->getParsedBody();

        foreach ($teams as $team) {
            $this->team_model->createTeam($team);
        }

        $response_data = array(
            "code" => "success",
            "message" => "The list of teams has been created successfully"
        );

        return $this->makeResponse($response, $response_data, 201);
    }

    public function handleUpdateTeam(Request $request, Response $response, array $uri_args): Response
    {
        $team = $request->getParsedBody();

        if (!is_null($team)) {
            $team_model = new TeamModel();
            foreach ($team as $key => $member) {
                $team_id = $member["team_id"];
                unset($member["team_id"]);
                $team_model->updateTeam($member, $team_id);
            }
        }

        $response_data = array(
            "code" => "success",
            "message" => "The list of teams updated correctly",
        );
        return $this->makeResponse($response, $response_data, 201);
    }

    public function handleDeleteTeam(Request $request, Response $response, array $uri_args): Response
    {
        $team_ids = $request->getParsedBody();
        $team_model = new TeamModel();
        foreach ($team_ids as $team_id) {
            $team_model->deleteTeam($team_id);
        }

        $response_data = array(
            "code" => "success",
            "message" => "The list of teams deleted correctly",
        );
        return $this->makeResponse($response, $response_data, 202);
    }

}
