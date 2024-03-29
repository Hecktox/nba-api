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

        if (isset($filters["matchup_home"])) {
            $sql .= " AND matchup_home LIKE CONCAT(:matchup_home, '%') ";
            $filter_values["matchup_home"] = $filters["matchup_home"];
        }

        if (isset($filters["wl_home"])) {
            $sql .= " AND wl_home = :wl_home ";
            $filter_values["wl_home"] = $filters["wl_home"];
        }

        if (isset($filters["min"])) {
            $sql .= " AND min = :min ";
            $filter_values["min"] = $filters["min"];
        }

        if (isset($filters["fgm_home"])) {
            $sql .= " AND fgm_home = :fgm_home ";
            $filter_values["fgm_home"] = $filters["fgm_home"];
        }

        if (isset($filters["fga_home"])) {
            $sql .= " AND fga_home = :fga_home ";
            $filter_values["fga_home"] = $filters["fga_home"];
        }

        if (isset($filters["fg_pct_home"])) {
            $sql .= " AND fg_pct_home = :fg_pct_home ";
            $filter_values["fg_pct_home"] = $filters["fg_pct_home"];
        }

        if (isset($filters["fg3m_home"])) {
            $sql .= " AND fg3m_home = :fg3m_home ";
            $filter_values["fg3m_home"] = $filters["fg3m_home"];
        }

        if (isset($filters["fg3a_home"])) {
            $sql .= " AND fg3a_home = :fg3a_home ";
            $filter_values["fg3a_home"] = $filters["fg3a_home"];
        }

        if (isset($filters["fg3_pct_home"])) {
            $sql .= " AND fg3_pct_home = :fg3_pct_home ";
            $filter_values["fg3_pct_home"] = $filters["fg3_pct_home"];
        }

        if (isset($filters["ftm_home"])) {
            $sql .= " AND ftm_home = :ftm_home ";
            $filter_values["ftm_home"] = $filters["ftm_home"];
        }

        if (isset($filters["fta_home"])) {
            $sql .= " AND fta_home = :fta_home ";
            $filter_values["fta_home"] = $filters["fta_home"];
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
        //! almost fixed

        $sql = "SELECT t_home.*, t_away.*
            FROM game g
            JOIN team t_home ON g.team_id_home = t_home.id
            JOIN team t_away ON g.`COL 30` = t_away.id
            WHERE g.game_id = :game_id";

        $result = $this->fetchAll($sql, ["game_id" => $game_id]);
        return is_array($result) ? $result : [];
    }
}
