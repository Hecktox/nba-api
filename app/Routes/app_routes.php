<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Vanier\Api\Controllers\AboutController;
use Vanier\Api\Controllers\DraftController;
use Vanier\Api\Controllers\GamesController;
use Vanier\Api\Controllers\PlayersController;
use Vanier\Api\Helpers\DateTimeHelper;
use Vanier\Api\Controllers\TeamController;

// Import the app instance into this file's scope.
global $app;

// TODO: Add your app's routes here.
//! The callbacks must be implemented in a controller class.
//! The Vanier\Api must be used as namespace prefix. 

// //* ROUTE: GET /Draft
// $app->get('/draft', [DraftController::class, 'handleGetDraft']);
// //* ROUTE: GET /Draft/{person_id}
// $app->get('/draft/{person_id}', [DraftController::class, 'handleGetDraftPersonId']);
// //* ROUTE: GET /Draft/{team_id}
// $app->get('/draft/{team_id}', [DraftController::class, 'handleGetDraftTeamId']);

//* ROUTE: GET /Team
$app->get('/team', [TeamController::class, 'handleGetAllTeams']);

//* ROUTE: GET /Team/{team_id}
$app->get('/team/{team_id}', [TeamController::class, 'handleGetTeamId']);

//* ROUTE: GET /Team/{team_id}/History
$app->get('/team/{team_id}/history', [TeamController::class, 'handleGetTeamHistory']);



//* ROUTE: GET /games
$app->get('/games', [GamesController::class, 'handleGetGames']);

//* ROUTE: GET /games/{game_id}
$app->get('/games/{game_id}', [GamesController::class, 'handleGetGameById']);

//* ROUTE: GET /games/{game_id}/teams
$app->get('/games/{game_id}/teams', [GamesController::class, 'handleGetGameTeams']);

//* ROUTE: POST /games
$app->post('/games', [GamesController::class, 'handleCreateGames']);

//* ROUTE: PUT /games
$app->put('/games', [GamesController::class, 'handleUpdateGames']);

//* ROUTE: DELETE /games
$app->delete('/games', [GamesController::class, 'handleDeleteGames']);



//*ROUTE: GET /players
$app->get('/players', [PlayersController::class, 'handleGetPlayers']);

//*ROUTE: GET /players/{player_id}
$app->get('/players/{player_id}', [PlayersController::class, 'handleGetPlayer']);

//*ROUTE: GET /players/{players_id}/drafts
$app->get('/players/{player_id}/drafts', [PlayersController::class, 'handleGetPlayerDrafts']);
