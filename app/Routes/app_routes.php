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
//* ROUTE: GET /Draf
$app->get('/draft', [DraftController::class, 'handleGetDraft']); 
//* ROUTE: GET /Team
$app->get('/team', [TeamController::class, 'handleGetAllTeams']);

//* ROUTE: GET /hello
$app->get('/hello', function (Request $request, Response $response, $args) {

    $now = DateTimeHelper::getDateAndTime(DateTimeHelper::D_M_Y);
    $response->getBody()->write("Reporting! Hello there! The current time is: " . $now);
    return $response;
});