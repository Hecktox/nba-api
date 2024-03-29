<?php

namespace Vanier\Api\Controllers;

use Vanier\Api\Models\GamesModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Vanier\Api\Exceptions\HttpInvalidPaginationParameterException;


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
}
