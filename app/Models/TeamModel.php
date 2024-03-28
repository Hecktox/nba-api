<?php

namespace Vanier\Api\Models;

class TeamModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
    }
    public function getAllTeams(array $filters): mixed
    {
        $filter_values = [];
        $sql = "SELECT t.*, th.year_founded, th.year_active_till 
                FROM team t 
                JOIN team_history th ON t.team_id = th.team_id 
                WHERE 1";

        if (isset($filters["full_name"])) {
            $sql .= " AND t.full_name LIKE CONCAT(:full_name, '%') ";
            $filter_values["full_name"] = $filters["full_name"];
        }
        if (isset($filters["nickname"])) {
            $sql .= " AND t.nickname LIKE CONCAT(:nickname, '%') ";
            $filter_values["nickname"] = $filters["nickname"];
        }
        if (isset($filters["abbreviation"])) {
            $sql .= " AND t.abbreviation LIKE CONCAT(:abbreviation, '%') ";
            $filter_values["abbreviation"] = $filters["abbreviation"];
        }
        if (isset($filters["city"])) {
            $sql .= " AND t.city LIKE CONCAT(:city, '%') ";
            $filter_values["city"] = $filters["city"];
        }
        if (isset($filters["state"])) {
            $sql .= " AND t.state LIKE CONCAT(:state, '%') ";
            $filter_values["state"] = $filters["state"];
        }
        if (isset($filters["year_founded"])) {
            $sql .= " AND th.year_founded LIKE CONCAT(:year_founded, '%') ";
            $filter_values["year_founded"] = $filters["year_founded"];
        }
        if (isset($filters["year_active_till"])) {
            $sql .= " AND th.year_active_till LIKE CONCAT(:year_active_till, '%') ";
            $filter_values["year_active_till"] = $filters["year_active_till"];
        }
        if (isset($filters["owner"])) {
            $sql .= " AND t.owner LIKE CONCAT(:owner, '%') ";
            $filter_values["owner"] = $filters["owner"];
        }
        
        // Sorting
        if (isset($filters["order"]) && in_array($filters["order"], ["asc", "desc"])) {
            $sql .= " ORDER BY t.full_name " . strtoupper($filters["order"]);
        }

        return (array) $this->paginate($sql, $filter_values);
    }

    public function getTeamInfo($team_id)
    {
        $sql = "SELECT * FROM team WHERE team_id = :team_id";
        return (array) $this->fetchSingle($sql, ["team_id" => $team_id]);
    }
}
