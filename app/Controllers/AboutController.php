<?php

declare(strict_types=1);

namespace Vanier\Api\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AboutController extends BaseController
{
    public function handleAboutWebService(Request $request, Response $response, array $uri_args): Response
    {
        $data = array(
            "description" => [
                "about" => "Welcome! This is a Web service that provides comprehensive access to NBA-related information, including player stats, team details, game schedules, and more. Our goal is to create a robust and efficient API for accessing and managing data related to the National Basketball Association (NBA). Whether you're a developer, analyst, or NBA enthusiast, our service aims to be your central hub for all things NBA-related. Explore our endpoints and discover the wealth of information available at your fingertips!",
                "authors" => [
                    "Muhammad Arsalan Saeed",
                    "Maximus Taube",
                    "David Jorge Sousa",
                    "Valentin Atanasov"
                ]
            ],
            "software" => [
                "dataset" => "https://www.kaggle.com/datasets/wyattowalsh/basketball",
                "database" => "http://localhost/nba-api/",
                "database_resources" => [
                    "team" => [
                        "uri" => "http://localhost/nba-api/team",
                        "methods" => ["GET", "POST", "PUT", "DELETE"]
                    ],
                    "team_id" => [
                        "uri" => "http://localhost/nba-api/team/{team_id}",
                        "methods" => ["GET"]
                    ],
                    "team_history" => [
                        "uri" => "http://localhost/nba-api/team/{team_id}/history",
                        "methods" => ["GET"]
                    ],
                    "players" => [
                        "uri" => "http://localhost/nba-api/players",
                        "methods" => ["GET", "POST", "PUT", "DELETE"]
                    ],
                    "player_id" => [
                        "uri" => "http://localhost/nba-api/team/{player_id}",
                        "methods" => ["GET"]
                    ],
                    "player_drafts" => [
                        "uri" => "http://localhost/nba-api/team/{player_id}/drafts",
                        "methods" => ["GET"]
                    ],
                    "drafts" => [
                        "uri" => "http://localhost/nba-api/draft",
                        "methods" => ["GET", "POST", "PUT", "DELETE"]
                    ],
                    "drafts_id" => [
                        "uri" => "http://localhost/nba-api/draft/{draft_id}",
                        "methods" => ["GET"]
                    ],
                    "draft_player_id" => [
                        "uri" => "http://localhost/nba-api/draft/{player_id}",
                        "methods" => ["GET"]
                    ],
                    "draft_seasons" => [
                        "uri" => "http://localhost/nba-api/draft/{player_id}/season",
                        "methods" => ["GET"]
                    ],
                    "games" => [
                        "uri" => "http://localhost/nba-api/games",
                        "methods" => ["GET", "POST", "PUT", "DELETE"]
                    ],
                    "game_id" => [
                        "uri" => "http://localhost/nba-api/games/{game_id}",
                        "methods" => ["GET"]
                    ],
                    "game_teams" => [
                        "uri" => "http://localhost/nba-api/games/{game_id}/teams",
                        "methods" => ["GET"]
                    ],
                    "sports" => [
                        "uri" => "http://localhost/nba-api/sports?c=[Canada]",
                        "methods" => ["GET"]
                    ],
                    "shows" => [
                        "uri" => "http://localhost/nba-api/shows",
                        "methods" => ["GET"]
                    ]
                ],
                "composite_resource_1" => "www.thesportsdb.com/api/v1/json/3/search_all_leagues.php?c=England",
                "composite_resource_2" => "https://api.tvmaze.com/search/shows?q=nba",
            ],
        );
        return $this->makeResponse($response, $data);
    }
}
