<?php

namespace Vanier\Api\Controllers;

use Vanier\Api\Models\GamesModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Vanier\Api\Exceptions\HttpInvalidPaginationParameterException;
use Vanier\Api\Validations\Validator;

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
            throw new HttpInvalidPaginationParameterException($request, "Invalid game id format. Must be at least an 8-digit number.");
        }
    }

    public function handleGetGameById(Request $request, Response $response, array $uri_args): Response
    {
        $game_id = $uri_args["game_id"];
        $this->assertGameId($request, $game_id);

        $game = $this->games_model->getGameById($game_id);

        if (empty($game)) {
            throw new HttpInvalidPaginationParameterException($request, "The supplied game id was not valid!");
        }

        return $this->makeResponse($response, $game);
    }

    public function handleGetGameTeams(Request $request, Response $response, array $uri_args): Response
    {
        $game_id = $uri_args["game_id"];
        $this->assertGameId($request, $game_id);

        $game_teams = $this->games_model->getGameTeams($game_id);

        if (empty($game_teams["game"])) {
            throw new HttpInvalidPaginationParameterException($request, "No game found with the provided id!");
        }

        return $this->makeResponse($response, $game_teams);
    }


    public function handleCreateGames(Request $request, Response $response, array $uri_args): Response
    {
        $games = $request->getParsedBody();

        $v = new Validator($games);
        $rules = array(
            'season_id' => [
                'integer',
                'required'
            ],
            'team_id_home' => [
                'integer',
                'required'
            ],
            'team_abbreviation_home' => [
                'required',
                array('regex', '^[A-Z][a-z]+$')
            ],
            'team_name_home' => [
                'required',
                array('regex', '^[A-Z][a-z]+$')
            ],
            'game_id' => [
                'integer',
                'required'
            ],
            'game_date' => [
                'required',
                'date'
            ],
            'pts_home' => [
                'required',
                'numeric'
            ],
            'plus_minus_home' => [
                'required',
                'numeric'
            ],
            'team_id_away' => [
                'integer',
                'required'
            ],
            'team_abbreviation_away' => [
                'required',
                array('regex', '^[A-Z][a-z]+$')
            ],
            'team_name_away' => [
                'required',
                array('regex', '^[A-Z][a-z]+$')
            ],
            'matchup_away' => [
                'required',
                array('regex', '^[A-Z][a-z]+$')
            ],
            'wl_away' => [
                'required',
                array('regex', '^[A-Z][a-z]+$')
            ],
            'pts_away' => [
                'required',
                'numeric'
            ],
            'plus_minus_away' => [
                'required',
                'numeric'
            ],
            'season_type' => [
                'required',
                array('regex', '^[A-Z][a-z]+$')
            ],
        );

        $v->mapFieldsRules($rules);

        if ($v->validate()) {
            foreach ($games as $game) {
                $this->games_model->createGame($game);
            }

            $response_data = array(
                "code" => "success",
                "message" => "The list of games has been created successfully"
            );

            return $this->makeResponse($response, $response_data, 201);
        } else {
            print_r($v->errors());
        }

        $response_data = array(
            "code" => "failure",
            "message" => "The list of games has not been created."
        );

        return $this->makeResponse($response, $response_data, 500);
    }

    public function handleUpdateGames(Request $request, Response $response, array $uri_args): Response
    {
        $games = $request->getParsedBody();

        $v = new Validator($games);
        $rules = array(
            'season_id' => [
                'integer',
                'required'
            ],
            'team_id_home' => [
                'integer',
                'required'
            ],
            'team_abbreviation_home' => [
                'required',
                array('regex', '^[A-Z][a-z]+$')
            ],
            'team_name_home' => [
                'required',
                array('regex', '^[A-Z][a-z]+$')
            ],
            'game_id' => [
                'integer',
                'required'
            ],
            'game_date' => [
                'required',
                'date'
            ],
            'pts_home' => [
                'required',
                'numeric'
            ],
            'plus_minus_home' => [
                'required',
                'numeric'
            ],
            'team_id_away' => [
                'integer',
                'required'
            ],
            'team_abbreviation_away' => [
                'required',
                array('regex', '^[A-Z][a-z]+$')
            ],
            'team_name_away' => [
                'required',
                array('regex', '^[A-Z][a-z]+$')
            ],
            'matchup_away' => [
                'required',
                array('regex', '^[A-Z][a-z]+$')
            ],
            'wl_away' => [
                'required',
                array('regex', '^[A-Z][a-z]+$')
            ],
            'pts_away' => [
                'required',
                'numeric'
            ],
            'plus_minus_away' => [
                'required',
                'numeric'
            ],
            'season_type' => [
                'required',
                array('regex', '^[A-Z][a-z]+$')
            ],
        );

        $v->mapFieldsRules($rules);

        if ($v->validate()) {
            foreach ($games as $game) {
                $game_id = $game["game_id"];
                unset($game["game_id"]);
                $this->games_model->updateGame($game, $game_id);
            }

            $response_data = array(
                "code" => "success",
                "message" => "The specified games have been updated successfully"
            );

            return $this->makeResponse($response, $response_data, 201);
        } else {
            print_r($v->errors());
        }

        $response_data = array(
            "code" => "failure",
            "message" => "The list of games has not been updated."
        );

        return $this->makeResponse($response, $response_data, 500);
    }

    public function handleDeleteGames(Request $request, Response $response, array $uri_args): Response
    {
        $games = $request->getParsedBody();

        $v = new Validator($games);
        $v->rule(function ($field, $value, $params, $fields) {
            // Custom validation logic
            return true;
        }, "")->message("{field} failed...");

        if ($v->validate()) {
            foreach ($games as $game_id) {
                $this->games_model->deleteGame($game_id);
            }

            $response_data = array(
                "code" => "success",
                "message" => "The specified games have been deleted successfully"
            );
            return $this->makeResponse($response, $response_data, 201);
        } else {
            print_r($v->errors());
        }

        $response_data = array(
            "code" => "failure",
            "message" => "The list of games has not been deleted."
        );

        return $this->makeResponse($response, $response_data, 500);
    }
}
