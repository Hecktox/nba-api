<?php

namespace Vanier\Api\Models;

class TeamModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
    }
    public function getAllTeams(array $filters): array
    {
        $filter_values = [];
        $sql = "SELECT * FROM teams WHERE 1";
    
        if (isset($filters["full_name"])) {
            $sql .= " AND full_name LIKE CONCAT(:full_name, '%') ";
            $filter_values["full_name"] = $filters["full_name"];
        }
        if (isset($filters["nickname"])) {
            $sql .= " AND nickname LIKE CONCAT(:nickname, '%') ";
            $filter_values["nickname"] = $filters["nickname"];
        }
        if (isset($filters["abbreviation"])) {
            $sql .= " AND abbreviation LIKE CONCAT(:abbreviation, '%') ";
            $filter_values["abbreviation"] = $filters["abbreviation"];
        }
        if (isset($filters["city"])) {
            $sql .= " AND city LIKE CONCAT(:city, '%') ";
            $filter_values["city"] = $filters["city"];
        }
        if (isset($filters["state"])) {
            $sql .= " AND state LIKE CONCAT(:state, '%') ";
            $filter_values["state"] = $filters["state"];
        }
        if (isset($filters["year_founded"])) {
            $sql .= " AND year_founded LIKE CONCAT(:year_founded, '%') ";
            $filter_values["year_founded"] = $filters["year_founded"];
        }
        if (isset($filters["year_active_till"])) {
            $sql .= " AND year_active_till LIKE CONCAT(:year_active_till, '%') ";
            $filter_values["year_active_till"] = $filters["year_active_till"];
        }
        if (isset($filters["owner"])) {
            $sql .= " AND owner LIKE CONCAT(:owner, '%') ";
            $filter_values["owner"] = $filters["owner"];
        }
    
        return (array) $this->paginate($sql, $filter_values);
    }

    // change later on so that year founded, 
    // year active till will no longer be in team but instead in team history

    public function getTeamInfo($team_id)
    {
        $sql = "SELECT * FROM teams WHERE team_id = :team_id";
        return (array) $this->fetchSingle($sql, ["team_id" => $team_id]);
    }

    
}
