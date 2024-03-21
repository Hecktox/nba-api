<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Vanier\Api\Controllers\AboutController;
use Vanier\Api\Controllers\DraftController;
use Vanier\Api\Helpers\DateTimeHelper;
use Vanier\Api\Controllers\TeamController;

// Import the app instance into this file's scope.
global $app;

// TODO: Add your app's routes here.
//! The callbacks must be implemented in a controller class.
//! The Vanier\Api must be used as namespace prefix. 

//* ROUTE: GET /
$app->get('/', [AboutController::class, 'handleAboutWebService']);
//* ROUTE: GET /Draft
$app->get('/draft', [DraftController::class, 'handleGetDraft']); 
//* ROUTE: GET /Draft/{person_id}
$app->get('/draft/{person_id}', [DraftController::class, 'handleGetDraftPersonId']); 
//* ROUTE: GET /Draft/{team_id}
$app->get('/draft/{team_id}', [DraftController::class, 'handleGetDraftTeamId']); 

//* ROUTE: GET /Team
$app->get('/team', [TeamController::class, 'handleGetAllTeams']);
$app->get('/team/{team_id}', [TeamController::class, 'handleGetTeamId']);

