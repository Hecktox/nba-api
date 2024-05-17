<?php

namespace Vanier\Api\Controllers;

use Vanier\Api\Models\GamesModel;
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

require_once 'validation/validation/Validator.php';

class GamesController extends BaseController
{
    private $games_model = null;

    public function __construct()
    {
        $this->games_model = new GamesModel();
    }

    public function handleGetGames(Request $request, Response $response, array $uri_args): Response
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

        $this->games_model->setPaginationOptions($page, $page_size);

        $games = $this->games_model->getAllGames($filters);

        return $this->makeResponse($response, $games);
    }

    private function assertGameId($request, $game_id)
    {
        if (!is_numeric($game_id) || strlen($game_id) < 8) {
            throw new HttpInvalidSyntaxException($request, "Invalid game id format. Must be at least an 8-digit number.");
        }
    }

    public function handleGetGameById(Request $request, Response $response, array $uri_args): Response
    {
        $game_id = $uri_args["game_id"];
        $this->assertGameId($request, $game_id);

        $game = $this->games_model->getGameById($game_id);

        if (empty($game)) {
            throw new HttpInvalidIdException($request, "The supplied game id was not valid!");
        }

        return $this->makeResponse($response, $game);
    }

    public function handleGetGameTeams(Request $request, Response $response, array $uri_args): Response
    {
        $game_id = $uri_args["game_id"];
        $this->assertGameId($request, $game_id);

        $game_teams = $this->games_model->getGameTeams($game_id);

        if (empty($game_teams["game"])) {
            throw new HttpInvalidIdException($request, "No game found with the provided id!");
        }

        return $this->makeResponse($response, $game_teams);
    }

    public function handleCreateGames(Request $request, Response $response, array $uri_args): Response
    {
        $games = $request->getParsedBody();

        foreach ($games as $game) {
            $this->validateCreateGame($game, $request);
            $this->games_model->createGame($game);
        }

        $response_data = array(
            "code" => "success",
            "message" => "The list of games has been created successfully"
        );

        return $this->makeResponse($response, $response_data, 201);
    }

    public function handleUpdateGames(Request $request, Response $response, array $uri_args): Response
    {
        $games = $request->getParsedBody();

        foreach ($games as $game) {
            $this->validateUpdateGame($game, $request);
            $game_id = $game["game_id"];
            unset($game["game_id"]);
            $this->games_model->updateGame($game, $game_id);
        }

        $response_data = array(
            "code" => "success",
            "message" => "The specified games have been updated successfully"
        );

        return $this->makeResponse($response, $response_data, 201);
    }

    public function handleDeleteGames(Request $request, Response $response, array $uri_args): Response
    {
        $games = $request->getParsedBody();

        $response_data = array(
            "code" => "error",
            "message" => "Field game_id is required and cannot be empty"
        );

        // Check if game_id field is missing or empty
        if (empty($games)) {
            // $response_data = array(
            //     "code" => "error",
            //     "message" => "Field game_id is required and cannot be empty"
            // );
            // return $this->makeResponse($response, $response_data, 400);
            throw new HttpRequiredFieldException($request, "Field game_id is required and cannot be empty");
        }

        foreach ($games as $game_id) {
            $this->validateDeleteGame($game_id, $request);
            $this->games_model->deleteGame($game_id);
        }

        $response_data = array(
            "code" => "success",
            "message" => "The specified games have been deleted successfully"
        );

        return $this->makeResponse($response, $response_data, 201);
    }

    private function validateCreateGame($game, $request)
    {
        // Check if fields exist in JSON (if they were initiated)
        $requiredFields = [
            'season_id',
            'team_id_home',
            'team_abbreviation_home',
            'team_name_home',
            'game_id',
            'game_date',
            'pts_home',
            'plus_minus_home',
            'team_id_away',
            'team_abbreviation_away',
            'team_name_away',
            'matchup_away',
            'wl_away',
            'pts_away',
            'plus_minus_away',
            'season_type'
        ];

        foreach ($requiredFields as $field) {
            if (!array_key_exists($field, $game)) {
                throw new HttpRequiredFieldException($request, 'Required field ' . $field . ' is missing');
            }
        }

        // Requires all fields, checks syntax
        $v = new Validator($game);
        $v->rule('required', [
            'season_id',
            'team_id_home',
            'team_abbreviation_home',
            'team_name_home',
            'game_id',
            'game_date',
            'pts_home',
            'plus_minus_home',
            'team_id_away',
            'team_abbreviation_away',
            'team_name_away',
            'matchup_away',
            'wl_away',
            'pts_away',
            'plus_minus_away',
            'season_type'
            // Checks format of fields
        ])->message('{field} is required')
            ->rule('integer', ['season_id', 'team_id_home', 'game_id', 'team_id_away'])
            ->message('{field} must be an integer')
            ->rule('date', 'game_date')
            ->message('Invalid date format for {field}')
            ->rule('numeric', ['pts_home', 'plus_minus_home', 'pts_away', 'plus_minus_away'])
            ->message('{field} must be numeric')
            ->rule('regex', 'season_id', '/^\d+$/')
            ->message('Invalid format for {field}')
            ->rule('regex', 'team_id_home', '/^\d+$/')
            ->message('Invalid format for {field}')
            ->rule('regex', 'team_abbreviation_home', '/^[A-Z]{3}$/')
            ->message('Invalid format for {field}')
            ->rule('regex', 'team_name_home', '/^[A-Za-z\s]+$/')
            ->message('Invalid format for {field}')
            ->rule('regex', 'game_id', '/^\d+$/')
            ->message('Invalid format for {field}')
            ->rule('regex', 'game_date', '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/')
            ->message('Invalid format for {field}')
            ->rule('regex', 'pts_home', '/^\d+(\.\d+)?$/')
            ->message('Invalid format for {field}')
            ->rule('regex', 'plus_minus_home', '/^-?\d+$/')
            ->message('Invalid format for {field}')
            ->rule('regex', 'team_id_away', '/^\d+$/')
            ->message('Invalid format for {field}')
            ->rule('regex', 'team_abbreviation_away', '/^[A-Z]{3}$/')
            ->message('Invalid format for {field}')
            ->rule('regex', 'team_name_away', '/^[A-Za-z\s]+$/')
            ->message('Invalid format for {field}')
            ->rule('regex', 'matchup_away', '/^[A-Z]{3} @ [A-Z]{3}$/')
            ->message('Invalid format for {field}')
            ->rule('regex', 'wl_away', '/^[WL]$/')
            ->message('Invalid format for {field}')
            ->rule('regex', 'pts_away', '/^\d+(\.\d+)?$/')
            ->message('Invalid format for {field}')
            ->rule('regex', 'plus_minus_away', '/^-?\d+$/')
            ->message('Invalid format for {field}')
            ->rule('regex', 'season_type', '/^[A-Za-z\s]+$/')
            ->message('Invalid format for {field}');

        // Check if fields are empty
        foreach ($game as $key => $value) {
            if (empty($value)) {
                throw new HttpRequiredFieldException($request, 'Field ' . $key . ' cannot be empty');
            }
        }

        // Check if game id already exists (if duplicate)
        if ($this->games_model->isGameIdDuplicate($game['game_id'])) {
            throw new HttpInvalidInputException($request, 'Duplicate game_id: ' . $game['game_id']);
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

    private function validateUpdateGame($game, $request)
    {
        // Check if game_id field is missing
        if (!isset($game['game_id'])) {
            throw new HttpRequiredFieldException($request, 'Field game_id is required');
        }

        $v = new Validator($game);
        // Checks syntax
        $v->rule('integer', ['season_id', 'team_id_home', 'game_id', 'team_id_away'])
            ->message('{field} must be an integer')
            ->rule('date', 'game_date')
            ->message('Invalid date format for {field}')
            ->rule('numeric', ['pts_home', 'plus_minus_home', 'pts_away', 'plus_minus_away'])
            ->message('{field} must be numeric')
            ->rule('regex', 'season_id', '/^\d+$/')
            ->message('Invalid format for {field}')
            ->rule('regex', 'team_id_home', '/^\d+$/')
            ->message('Invalid format for {field}')
            ->rule('regex', 'team_abbreviation_home', '/^[A-Z]{3}$/')
            ->message('Invalid format for {field}')
            ->rule('regex', 'team_name_home', '/^[A-Za-z\s]+$/')
            ->message('Invalid format for {field}')
            ->rule('regex', 'game_id', '/^\d+$/')
            ->message('Invalid format for {field}')
            ->rule('regex', 'game_date', '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/')
            ->message('Invalid format for {field}')
            ->rule('regex', 'pts_home', '/^\d+(\.\d+)?$/')
            ->message('Invalid format for {field}')
            ->rule('regex', 'plus_minus_home', '/^-?\d+$/')
            ->message('Invalid format for {field}')
            ->rule('regex', 'team_id_away', '/^\d+$/')
            ->message('Invalid format for {field}')
            ->rule('regex', 'team_abbreviation_away', '/^[A-Z]{3}$/')
            ->message('Invalid format for {field}')
            ->rule('regex', 'team_name_away', '/^[A-Za-z\s]+$/')
            ->message('Invalid format for {field}')
            ->rule('regex', 'matchup_away', '/^[A-Z]{3} @ [A-Z]{3}$/')
            ->message('Invalid format for {field}')
            ->rule('regex', 'wl_away', '/^[WL]$/')
            ->message('Invalid format for {field}')
            ->rule('regex', 'pts_away', '/^\d+(\.\d+)?$/')
            ->message('Invalid format for {field}')
            ->rule('regex', 'plus_minus_away', '/^-?\d+$/')
            ->message('Invalid format for {field}')
            ->rule('regex', 'season_type', '/^[A-Za-z\s]+$/')
            ->message('Invalid format for {field}');

        // Check if any fields are empty
        foreach ($game as $key => $value) {
            if (empty($value)) {
                throw new HttpRequiredFieldException($request, 'Field ' . $key . ' cannot be empty');
            }
        }

        // Check if game_id exists
        if (!$this->games_model->getGameById($game['game_id'])) {
            throw new HttpInvalidIdException($request, 'Game with game_id ' . $game['game_id'] . ' does not exist');
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

    private function validateDeleteGame($game_id, $request)
    {
        // Check if game_id exists
        if (!$this->games_model->getGameById($game_id)) {
            throw new HttpInvalidIdException($request, 'Game with game_id ' . $game_id . ' does not exist');
        }

        // Check if game_id is an integer
        if (!is_numeric($game_id)) {
            throw new HttpInvalidSyntaxException($request, 'Field game_id must be a valid integer');
        }
    }
}