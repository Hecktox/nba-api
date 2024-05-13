<?php

namespace Vanier\Api\Controllers;

use Vanier\Api\Models\TeamModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Vanier\Api\Exceptions\HttpInvalidPaginationParameterException;
use Vanier\Api\Validations\Validator;
use Vanier\Api\Exceptions\HttpInvalidInputException;

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
        $rules = array(
            'team_id' => [
                'integer'
            ],
            'full_name' => [
                array('regex', '/^[A-Z][a-zA-Z\s]+$/')
            ],
            'abbreviation' => [
                array('regex', '/^[A-Z]+$/')
            ],
            'nickname' => [
                array('regex', '/^[A-Z][a-zA-Z\s]+$/')
            ],
            'city' => [
                array('regex', '/^[A-Z][a-zA-Z\s]+$/')
            ],
            'state' => [
                array('regex', '/^[A-Z][a-zA-Z\s]+$/')
            ],
            'year_founded' => [
                'integer'
            ],
            'owner' => [
                array('regex', '/^[A-Z][a-zA-Z\s]+$/')
            ],
            'year_active_till' => [
                ['regex', '/^\d{4}$/']
            ]
        );

        $v->mapFieldsRules($rules);

        if ($v->validate()) {
            foreach ($teams as $team) {
                $this->team_model->createTeam($team);
            }

            $response_data = array(
                "code" => "success",
                "message" => "The list of teams has been created successfully"
            );

            return $this->makeResponse($response, $response_data, 201);
        } else {
            $errors = $v->errors();
            if (isset($errors['team_id']) && in_array('integer', $errors['team_id'])) {
                throw new HttpInvalidInputException($request);
            }

            print_r($errors);
        }

        $response_data = array(
            "code" => "failure",
            "message" => "The list of teams has not been created."
        );

        return $this->makeResponse($response, $response_data, 500);
    }
    public function handleUpdateTeam(Request $request, Response $response, array $uri_args): Response
    {
        $teams = $request->getParsedBody();

        $v = new Validator($teams);
        $rules = array(
            'team_id' => [
                'integer'
            ],
            'full_name' => [
                array('regex', '/^[A-Z][a-zA-Z\s]+$/')
            ],
            'abbreviation' => [
                array('regex', '/^[A-Z]+$/')
            ],
            'nickname' => [
                array('regex', '/^[A-Z][a-zA-Z\s]+$/')
            ],
            'city' => [
                array('regex', '/^[A-Z][a-zA-Z\s]+$/')
            ],
            'state' => [
                array('regex', '/^[A-Z][a-zA-Z\s]+$/')
            ],
            'year_founded' => [
                'integer'
            ],
            'owner' => [
                array('regex', '/^[A-Z][a-zA-Z\s]+$/')
            ],
            'year_active_till' => [
                ['regex', '/^\d{4}$/']
            ]
        );

        $v->mapFieldsRules($rules);

        if ($v->validate()) {
            foreach ($teams as $team) {
                $team_id = $team["team_id"];
                unset($team["team_id"]);
                $this->team_model->updateTeam($team, $team_id);
            }

            $response_data = array(
                "code" => "success",
                "message" => "The specified teams have been updated successfully"
            );

            return $this->makeResponse($response, $response_data, 201);
        } else {
            $errors = $v->errors();
            if (isset($errors['team_id']) && in_array('integer', $errors['team_id'])) {
                throw new HttpInvalidInputException($request);
            }

            print_r($errors);
        }

        $response_data = array(
            "code" => "failure",
            "message" => "The list of teams has not been updated."
        );

        return $this->makeResponse($response, $response_data, 500);
    }

    public function handleDeleteTeam(Request $request, Response $response, array $uri_args): Response
    {
        $teams = $request->getParsedBody();

        $v = new Validator($teams);
        $v->rule(function ($field, $value, $params, $fields) {
            return true;
        }, "")->message("{field} failed...");

        if ($v->validate()) {
            foreach ($teams as $team_id) {
                $this->team_model->deleteTeam($team_id);
            }

            $response_data = array(
                "code" => "success",
                "message" => "The specified teams have been deleted successfully"
            );
            return $this->makeResponse($response, $response_data, 201);
        } else {
            $errors = $v->errors();
            if (isset($errors['team_id']) && in_array('integer', $errors['team_id'])) {
                throw new HttpInvalidInputException($request);
            }

            print_r($errors);
        }

        $response_data = array(
            "code" => "failure",
            "message" => "The list of teams has not been deleted."
        );

        return $this->makeResponse($response, $response_data, 500);
    }
}
