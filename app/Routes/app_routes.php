<?php

declare(strict_types=1);

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Vanier\Api\Controllers\AboutController;
use Vanier\Api\Controllers\AccountsController;
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

//* ROUTE: GET /Draft
$app->get('/draft', [DraftController::class, 'handleGetDraft']);
//* ROUTE: GET /Draft/{player_id}
$app->get('/draft/{player_id}', [DraftController::class, 'handleGetDraftPlayerId']);
//* ROUTE: GET /Draft/{player_id}/season
$app->get('/draft/{player_id}/season', [DraftController::class, 'handleGetPlayerIdSeason']);
$app->post('/draft', [DraftController::class, 'handleCreateDraft']);
//* ROUTE: PUT /games
$app->put('/draft', [DraftController::class, 'handleUpdateDraft']);
//* ROUTE: DELETE /games
$app->delete('/draft', [DraftController::class, 'handleDeleteDraft']);


//* ROUTE: CURM /Team
$app->get('/team', [TeamController::class, 'handleGetAllTeams']);
$app->post('/team', [TeamController::class, 'handleCreateTeam']); 
$app->put('/team', [TeamController::class, 'handleUpdateTeam']); 
$app->delete('/team', [TeamController::class, 'handleDeleteTeam']); 
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

//*ROUTE: POST /players
$app->post('/players', [PlayersController::class, 'handleCreatePlayers']);

//*ROUTE: PUT /players
$app->put('/players', [PlayersController::class, 'handleUpdatePlayers']);

//*ROUTE: DELETE /players
$app->delete('/players', [PlayersController::class, 'handleDeletePlayers']);

//*ROUTE: GET /players/{player_id}
$app->get('/players/{player_id}', [PlayersController::class, 'handleGetPlayer']);
//*ROUTE: GET /players/{players_id}/drafts
$app->get('/players/{player_id}/drafts', [PlayersController::class, 'handleGetPlayerDrafts']);

//*ROUTE: GET /sport
$app->get('/sports', [SportDbController::class, 'searchLeagues'])
    
//*ROUTE: POST /account
$app->post('/account', [AccountsController::class, 'handleCreateAccount']);
//*ROUTE: POST /token
$app->post('/token', [AccountsController::class, 'handleGenerateToken']);

//! For logging (example!). We need to implement this in helper and index.
//* ROUTE: GET /hello
$app->get('/hello', function (Request $request, Response $response, $args) {

    // 1) Initiate and configure a logger.
    $logger = new Logger('access');
    $logger->pushHandler(
        new StreamHandler(
            APP_LOGS_DIR . APP_ACCESS_LOG_FILE,
            Logger::DEBUG
        )
    );

    // 2) We now can log some access info:
    $client_ip = $_SERVER["REMOTE_ADDR"];
    $method = $request->getMethod();
    $uri = $request->getUri()->getPath();
    $log_record = $client_ip . ' ' . $method . ' ' . $uri . ' ' . "Hello!";

    // 3) Prepare any extra info.
    $extras = $request->getQueryParams();

    $logger->info($log_record, $extras);

    $now = DateTimeHelper::getDateAndTime(DateTimeHelper::D_M_Y);
    $response->getBody()->write("Reporting! Hello there! The current time is: " . $now);
    return $response;
});
