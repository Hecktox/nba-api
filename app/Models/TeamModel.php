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
        //slq stament
        $filter_value = [];
        $sql = "SELECT * FROM team t, team_history th, td team_details WHERE 
            t.team_id = th.team_id 
        AND t.team_id = td.team_id";
        
        //filtering
        if (isset($filters["full_name"])) {
            $sql .= " AND full_name LIKE CONCAT( :full_name, '%')";
            $filter_value["full_name"] = $filters["full_name"];
        }
        if (isset($filters["nickname"])) {
            $sql .= " AND nickname LIKE CONCAT( :nickname   , '%')";
            $filter_value["nickname"] = $filters["nickname"];
        }
        if (isset($filters["abbreviation"])) {
            $sql .= " AND abbreviation LIKE CONCAT( :abbreviation, '%')";
            $filter_value["abbreviation"] = $filters["abbreviation"];
        }
        if (isset($filters["city"])) {
            $sql .= " AND city LIKE CONCAT( :city, '%')";
            $filter_value["city"] = $filters["city"];
        }
        if (isset($filters["state"])) {
            $sql .= " AND state LIKE CONCAT( :state, '%')";
            $filter_value["state"] = $filters["state"];
        }
        if (isset($filters["year_founded"])) {
            $sql .= " AND year_founded LIKE CONCAT( :year_founded, '%')";
            $filter_value["year_founded"] = $filters["year_founded"];
        }
        if (isset($filters["year_active_till"])) {
            $sql .= " AND year_active_till LIKE CONCAT( :year_active_till, '%')";
            $filter_value["year_active_till"] = $filters["year_active_till"];
        }
        if (isset($filters["owner"])) {
            $sql .= " AND owner LIKE CONCAT( :owner, '%')";
            $filter_value["owner"] = $filters["owner"];
        }
        return (array) $this->paginate($sql, $filter_value);
    }
}
