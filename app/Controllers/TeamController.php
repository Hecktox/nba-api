<?php

namespace Vanier\Api\Controllers;

use Vanier\Api\Models\TeamModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Vanier\Api\Exceptions\HttpInvalidPaginationParameterException;
use Vanier\Api\Validations\Validator;
use Vanier\Api\Exceptions\HttpInvalidInputException;
use Vanier\Api\Exceptions\HttpInvalidSyntaxException;
use Vanier\Api\Exceptions\HttpNoContentException;
use Vanier\Api\Exceptions\HttpInvalidIdException;
use Vanier\Api\Exceptions\HttpForeignKeyException;
use Vanier\Api\Exceptions\HttpRequiredFieldException;

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
            $response_data = array(
                "code" => "failure",
                "message" => "No team information found for the supplied team ID"
            );
            return $this->makeResponse($response, $response_data, 404);
        }

        return $this->makeResponse($response, $team_info);
    }

    public function handleGetTeamHistory(Request $request, Response $response, array $uri_args): Response
    {
        $team_id = $uri_args["team_id"];

        $this->assertTeamId($request, $team_id);

        if (!$this->team_model->verifyTeamId($team_id)) {
            throw new HttpInvalidInputException(
                $request,
                "The supplied team ID is not valid"
            );
        }

        $team_info = $this->team_model->getTeamHistory($team_id);

        if (empty($team_info['team'])) {
            $response_data = array(
                "code" => "failure",
                "message" => "No team history found for the supplied team ID"
            );
            return $this->makeResponse($response, $response_data, 404);
        }

        return $this->makeResponse($response, $team_info);
    }

    public function handleCreateTeam(Request $request, Response $response, array $uri_args): Response
    {
        $teams = $request->getParsedBody();

        foreach ($teams as $team) {
            $this->validateCreateTeam($team, $request);
            $this->team_model->createTeam($team);
        }

        $response_data = array(
            "code" => "success",
            "message" => "The list of team has been created successfully"
        );

        return $this->makeResponse($response, $response_data, 201);
    }

    private function validateCreateTeam($team, $request)
    {
        // Check if fields exist in JSON (if they were initiated)
        $requiredFields = [
            'team_id',
            'full_name',
            'abbreviation',
            'nickname',
            'city',
            'state',
            'year_founded',
            'owner',
            'year_active_till'
        ];

        foreach ($requiredFields as $field) {
            if (!array_key_exists($field, $team)) {
                throw new HttpRequiredFieldException($request, 'Required field ' . $field . ' is missing');
            }
        }

        // Requires all fields, checks syntax
        $v = new Validator($team);
        $v->rule('required', $requiredFields)->message('{field} is required')
            ->rule('integer', ['team_id', 'year_founded'])->message('{field} must be an integer')
            ->rule('regex', 'full_name', '/^[A-Z][a-zA-Z\s]+$/')->message('Invalid format for {field}')
            ->rule('regex', 'abbreviation', '/^[A-Z]+$/')->message('Invalid format for {field}')
            ->rule('regex', 'nickname', '/^[A-Z][a-zA-Z\s]+$/')->message('Invalid format for {field}')
            ->rule('regex', 'city', '/^[A-Z][a-zA-Z\s]+$/')->message('Invalid format for {field}')
            ->rule('regex', 'state', '/^[A-Z][a-zA-Z\s]+$/')->message('Invalid format for {field}')
            ->rule('regex', 'owner', '/^[A-Z][a-zA-Z\s]+$/')->message('Invalid format for {field}')
            ->rule('regex', 'year_active_till', '/^\d{4}$/')->message('Invalid format for {field}');

        // Check if fields are empty
        foreach ($team as $key => $value) {
            if (empty($value)) {
                throw new HttpRequiredFieldException($request, 'Field ' . $key . ' cannot be empty');
            }
        }

        // Check if team id already exists (if duplicate)
        if ($this->team_model->verifyTeamId($team['team_id'])) {
            throw new HttpInvalidInputException($request, 'Duplicate team_id: ' . $team['team_id']);
        }

        if (!$v->validate()) {
            $errors = $v->errors();
            $errorMessages = [];
            foreach ($errors as $field => $error) {
                $errorMessages[] = $error[0];
            }
            throw new HttpInvalidInputException($request, 'Validation error: ' . implode(', ', $errorMessages));
        }
    }


    public function handleUpdateTeam(Request $request, Response $response, array $uri_args): Response
    {
        $teams = $request->getParsedBody();

        foreach ($teams as $team) {
            $this->validateUpdateTeam($team, $request);
            $team_id = $team["team_id"];
            unset($team["team_id"]);
            $this->team_model->updateTeam($team, $team_id);
        }

        $response_data = array(
            "code" => "success",
            "message" => "The specified teams have been updated successfully"
        );

        return $this->makeResponse($response, $response_data, 201);
    }

    private function validateUpdateTeam($team, $request)
    {
        // Check if team_id field is missing
        if (!isset($team['team_id'])) {
            throw new HttpRequiredFieldException($request, 'Field team_id is required');
        }

        $v = new Validator($team);
        // Checks syntax
        $v->rule('integer', ['team_id', 'year_founded'])->message('{field} must be an integer')
            ->rule('regex', 'full_name', '/^[A-Z][a-zA-Z\s]+$/')->message('Invalid format for {field}')
            ->rule('regex', 'abbreviation', '/^[A-Z]+$/')->message('Invalid format for {field}')
            ->rule('regex', 'nickname', '/^[A-Z][a-zA-Z\s]+$/')->message('Invalid format for {field}')
            ->rule('regex', 'city', '/^[A-Z][a-zA-Z\s]+$/')->message('Invalid format for {field}')
            ->rule('regex', 'state', '/^[A-Z][a-zA-Z\s]+$/')->message('Invalid format for {field}')
            ->rule('regex', 'owner', '/^[A-Z][a-zA-Z\s]+$/')->message('Invalid format for {field}')
            ->rule('regex', 'year_active_till', '/^\d{4}$/')->message('Invalid format for {field}');

        // Check if any fields are empty
        foreach ($team as $key => $value) {
            if (empty($value)) {
                throw new HttpRequiredFieldException($request, 'Field ' . $key . ' cannot be empty');
            }
        }

        // Check if team_id exists
        if (!$this->team_model->getTeamInfo($team['team_id'])) {
            throw new HttpInvalidIdException($request, 'Team with team_id ' . $team['team_id'] . ' does not exist');
        }

        if (!$v->validate()) {
            $errors = $v->errors();
            $errorMessages = [];
            foreach ($errors as $field => $error) {
                $errorMessages[] = $error[0];
            }
            throw new HttpInvalidInputException($request, 'Validation error: ' . implode(', ', $errorMessages));
        }
    }


    public function handleDeleteTeam(Request $request, Response $response, array $uri_args): Response
    {
        $teams = $request->getParsedBody();

        $response_data = array(
            "code" => "error",
            "message" => "Field team_id is required and cannot be empty"
        );

        // Check if team_id field is missing or empty
        if (empty($teams)) {
            // $response_data = array(
            //     "code" => "error",
            //     "message" => "Field team_id is required and cannot be empty"
            // );
            // return $this->makeResponse($response, $response_data, 400);
            throw new HttpRequiredFieldException($request, "Field team_id is required and cannot be empty");
        }

        foreach ($teams as $team_id) {
            $this->validateDeleteTeam($team_id, $request);
            $this->team_model->deleteTeam($team_id);
        }

        $response_data = array(
            "code" => "success",
            "message" => "The specified teams have been deleted successfully"
        );

        return $this->makeResponse($response, $response_data, 201);
    }

    private function validateDeleteTeam($team_id, $request)
    {
        // Check if team_id exists
        if (!$this->team_model->getTeamInfo($team_id)) {
            throw new HttpInvalidIdException($request, 'Team with team_id ' . $team_id . ' does not exist');
        }

        // Check if team_id is an integer
        if (!is_numeric($team_id)) {
            throw new HttpInvalidSyntaxException($request, 'Field team_id must be a valid integer');
        }
    }
}
