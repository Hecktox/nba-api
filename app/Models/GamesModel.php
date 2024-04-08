<?php

namespace Vanier\Api\Models;

class GamesModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAllGames(array $filters): array
    {
        $filter_values = [];
        $sql = "SELECT * FROM game WHERE 1 ";

        if (isset($filters["season_id"])) {
            $sql .= " AND season_id = :season_id ";
            $filter_values["season_id"] = $filters["season_id"];
        }

        if (isset($filters["team_id_home"])) {
            $sql .= " AND team_id_home = :team_id_home ";
            $filter_values["team_id_home"] = $filters["team_id_home"];
        }

        if (isset($filters["team_abbreviation_home"])) {
            $sql .= " AND team_abbreviation_home LIKE CONCAT(:team_abbreviation_home, '%') ";
            $filter_values["team_abbreviation_home"] = $filters["team_abbreviation_home"];
        }

        if (isset($filters["team_name_home"])) {
            $sql .= " AND team_name_home LIKE CONCAT(:team_name_home, '%') ";
            $filter_values["team_name_home"] = $filters["team_name_home"];
        }

        if (isset($filters["game_date"])) {
            $sql .= " AND game_date = :game_date ";
            $filter_values["game_date"] = $filters["game_date"];
        }

        if (isset($filters["matchup_away"])) {
            $sql .= " AND matchup_away LIKE CONCAT(:matchup_away, '%') ";
            $filter_values["matchup_away"] = $filters["matchup_away"];
        }

        if (isset($filters["wl_away"])) {
            $sql .= " AND wl_away = :wl_away ";
            $filter_values["wl_away"] = $filters["wl_away"];
        }

        if (isset($filters["pts_away"])) {
            $sql .= " AND pts_away = :pts_away ";
            $filter_values["pts_away"] = $filters["pts_away"];
        }

        if (isset($filters["plus_minus_away"])) {
            $sql .= " AND plus_minus_away = :plus_minus_away ";
            $filter_values["plus_minus_away"] = $filters["plus_minus_away"];
        }

        if (isset($filters["season_type"])) {
            $sql .= " AND season_type = :season_type ";
            $filter_values["season_type"] = $filters["season_type"];
        }

        if (isset($filters["order"]) && in_array($filters["order"], ["asc", "desc"])) {
            $sql .= " ORDER BY game_date " . strtoupper($filters["order"]);
        }

        return (array) $this->paginate($sql, $filter_values);
    }

    public function getGameById($game_id): mixed
    {
        $sql = "SELECT * FROM game WHERE game_id = :game_id";
        return $this->fetchSingle($sql, ["game_id" => $game_id]);
    }

    public function getGameTeams($game_id): array
    {
        $result = array();

        $sql = "SELECT * FROM game WHERE game_id = :game_id";
        $game = $this->fetchSingle($sql, ["game_id" => $game_id]);

        if (!$game) {
            return $result;
        }

        $result["game"] = $game;

        $sql_home = "SELECT * FROM team WHERE team_id = :team_id";
        $team_home = $this->fetchSingle($sql_home, ["team_id" => $game["team_id_home"]]);
        $result["team_home"] = $team_home;

        $sql_away = "SELECT * FROM team WHERE team_id = :team_id";
        $team_away = $this->fetchSingle($sql_away, ["team_id" => $game["team_id_away"]]);
        $result["team_away"] = $team_away;

        return $result;
    }

    public function createGame(array $game_data): mixed
    {
        return $this->insert("game", $game_data);
    }

    public function updateGame(array $game_data, int $game_id): mixed
    {
        return $this->update("game", $game_data, ["game_id" => $game_id]);
    }

    public function deleteGame(int $game_id): mixed
    {
        return $this->delete("game", ["game_id" => $game_id]);
    }
}
