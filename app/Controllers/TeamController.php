<?php

namespace Vanier\Api\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Vanier\Api\Models\TeamModel;
use Vanier\Api\Exceptions\HttpInvalidInputException;
use Vanier\Api\Exceptions\HttpInvalidPaginationParameterException;
use Vanier\Api\Validations\Validator;

require_once("validation/validation/Validator.php");

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

        $this->assertTeamId($request, $team_id);

        if (!$this->team_model->verifyTeamId($team_id)) {
            throw new HttpInvalidInputException(
                $request,
                "The supplied team ID is not valid"
            );
        }

        $team_info = $this->team_model->getTeamInfo($team_id);

        if (empty($team_info)) {
            throw new HttpInvalidInputException(
                $request,
                "No team information found for the supplied team ID"
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

        $v = new Validator($teams);
        $rules = [
            'team_id' => [
                'required',
                'integer',
                ['min', 10]
            ],
            'full_name' => [
                'required',
                'string',
                ['max', 255]
            ],
            'abbreviation' => [
                'required',
                'string',
                ['max', 10]
            ],
            'nickname' => [
                'required',
                'string'
            ],
            'city' => [
                'required',
                'string'
            ],
            'state' => [
                'required',
                'string'
            ],
            'year_founded' => [
                'required',
                'string',
                ['regex', '/^\d{4}$/'] // Year format (e.g., 2024)
            ],
            'owner' => [
                'required',
                'string'
            ],
            'year_active_till' => [
                'required',
                'string',
                ['regex', '/^\d{4}$/'] // Year format (e.g., 2024)
            ]
        ];

        $v->mapFieldsRules($rules);

        if ($v->validate()) {
            foreach ($teams as $team) {
                $this->team_model->createTeam($team);
            }

            $response_data = [
                "code" => "success",
                "message" => "The list of teams has been created successfully"
            ];

            return $this->makeResponse($response, $response_data, 201);
        } else {
            $response_data = [
                "code" => "failure",
                "message" => "Invalid team data"
            ];

            return $this->makeResponse($response, $response_data, 400);
        }
    }

    // public function handleCreateTeam(Request $request, Response $response, array $uri_args): Response
    // {
    //     $teams = $request->getParsedBody();

    //     foreach ($teams as $team) {
    //         $this->team_model->createTeam($team);
    //     }

    //     $response_data = array(
    //         "code" => "success",
    //         "message" => "The list of teams has been created successfully"
    //     );

    //     return $this->makeResponse($response, $response_data, 201);
    // }


    // public function handleUpdateTeam(Request $request, Response $response, array $uri_args): Response
    // {
    //     $team = $request->getParsedBody();

    //     if (!is_null($team)) {
    //         $team_model = new TeamModel();
    //         foreach ($team as $key => $member) {
    //             $team_id = $member["team_id"];
    //             unset($member["team_id"]);
    //             $team_model->updateTeam($member, $team_id);
    //         }
    //     }

    //     $response_data = array(
    //         "code" => "success",
    //         "message" => "The list of teams updated correctly",
    //     );
    //     return $this->makeResponse($response, $response_data, 201);
    // }

    public function handleUpdateTeam(Request $request, Response $response, array $uri_args): Response
    {
        $team = $request->getParsedBody();

        // Validate team data
        $v = new Validator($team);
        $rules = [
            'team_id' => [
                'required',
                'integer',
                ['min', 10]
            ],
            'full_name' => [
                'required',
                'string',
                ['max', 255]
            ],
            'abbreviation' => [
                'required',
                'string',
                ['max', 10]
            ],
            'nickname' => [
                'required',
                'string'
            ],
            'city' => [
                'required',
                'string'
            ],
            'state' => [
                'required',
                'string'
            ],
            'year_founded' => [
                'required',
                'string',
                ['regex', '/^\d{4}$/'] // Year format (e.g., 2024)
            ],
            'owner' => [
                'required',
                'string'
            ],
            'year_active_till' => [
                'required',
                'string',
                ['regex', '/^\d{4}$/'] // Year format (e.g., 2024)
            ]
        ];

        $v->mapFieldsRules($rules);

        // How to throw an appropriate exception
        if (!$v->validate()) {
            throw new HttpInvalidInputException($request, 'Invalid team data.');
        }

        if (!is_null($team)) {
            $team_model = new TeamModel();
            foreach ($team as $member) {
                $team_id = $member["team_id"];
                unset($member["team_id"]);
                $team_model->updateTeam($member, $team_id);
            }
        }

        $response_data = [
            "code" => "success",
            "message" => "The list of teams updated correctly",
        ];
        return $this->makeResponse($response, $response_data, 201);
    }


    public function handleDeleteTeam(Request $request, Response $response, array $uri_args): Response
    {
        $team_ids = $request->getParsedBody();

        // Validate team IDs (assuming it's an array of integers)
        $v = new Validator($team_ids);
        $v->rule('each', 'integer');
        if (!$v->validate()) {
            throw new HttpInvalidInputException($request, "Invalid team ID(s) provided.");
        }

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

    // public function handleDeleteTeam(Request $request, Response $response, array $uri_args): Response
    // {
    //     $team_ids = $request->getParsedBody();
    //     $team_model = new TeamModel();
    //     foreach ($team_ids as $team_id) {
    //         $team_model->deleteTeam($team_id);
    //     }

    //     $response_data = array(
    //         "code" => "success",
    //         "message" => "The list of teams deleted correctly",
    //     );
    //     return $this->makeResponse($response, $response_data, 202);
    // }
}
