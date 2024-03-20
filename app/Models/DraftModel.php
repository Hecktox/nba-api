<?php

namespace Vanier\Api\Models;

class DraftModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
    }
    public function getAllDraftStats(array $filters): array
    {
        //slq stament
        $filter_value = [];
        $sql = "SELECT * FROM draft_history WHERE 1";
        
        //filtering
        if (isset($filters["first_name"])) {
            $sql .= " AND first_name LIKE CONCAT( :first_name, '%')";
            $filter_value["first_name"] = $filters["first_name"];
        }
        if (isset($filters["last_name"])) {
            $sql .= " AND last_name LIKE CONCAT( :last_name, '%')";
            $filter_value["last_name"] = $filters["last_name"];
        }
        if (isset($filters["player_name"])) {
            $sql .= " AND player_name LIKE CONCAT( :player_name, '%')";
            $filter_value["player_name"] = $filters["player_name"];
        }
        if (isset($filters["position"])) {
            $sql .= " AND position LIKE CONCAT( :position, '%')";
            $filter_value["position"] = $filters["position"];
        }
        if (isset($filters["weight"])) {
            $sql .= " AND weight LIKE CONCAT( :weight, '%')";
            $filter_value["weight"] = $filters["weight"];
        }
        if (isset($filters["wingspan"])) {
            $sql .= " AND wingspan LIKE CONCAT( :wingspan, '%')";
            $filter_value["wingspan"] = $filters["wingspan"];
        }
        if (isset($filters["standing_reach"])) {
            $sql .= " AND standing_reach LIKE CONCAT( :standing_reach, '%')";
            $filter_value["standing_reach"] = $filters["standing_reach"];
        }
        if (isset($filters["hand_lenght"])) {
            $sql .= " AND hand_lenght LIKE CONCAT( :hand_lenght, '%')";
            $filter_value["hand_lenght"] = $filters["hand_lenght"];
        }
        if (isset($filters["hand_width"])) {
            $sql .= " AND hand_width LIKE CONCAT( :hand_width, '%')";
            $filter_value["hand_width"] = $filters["hand_width"];
        }
        if (isset($filters["standing_vertical_leap"])) {
            $sql .= " AND standing_vertical_leap LIKE CONCAT( :standing_vertical_leap, '%')";
            $filter_value["standing_vertical_leap"] = $filters["standing_vertical_leap"];
        }
        if (isset($filters["max_vertical_leap"])) {
            $sql .= " AND max_vertical_leap LIKE CONCAT( :max_vertical_leap, '%')";
            $filter_value["max_vertical_leap"] = $filters["max_vertical_leap"];
        }
        if (isset($filters["bench_press"])) {
            $sql .= " AND bench_press LIKE CONCAT( :bench_press, '%')";
            $filter_value["bench_presss"] = $filters["bench_press"];
        }
        return (array) $this->paginate($sql, $filter_value);
    }
    public function getDraftPersonId($person_id,array $filters): array
    {
        $result = array();
        $sql = "SELECT * FROM draft_history WHERE person_id = :person_id";
        $filter_value = ["person_id" => $person_id];
        //filtering
        if (isset($filters["first_name"])) {
            $sql .= " AND first_name LIKE CONCAT( :first_name, '%')";
            $filter_value["first_name"] = $filters["first_name"];
        }
        if (isset($filters["last_name"])) {
            $sql .= " AND last_name LIKE CONCAT( :last_name, '%')";
            $filter_value["last_name"] = $filters["last_name"];
        }
        if (isset($filters["player_name"])) {
            $sql .= " AND player_name LIKE CONCAT( :player_name, '%')";
            $filter_value["player_name"] = $filters["player_name"];
        }
        if (isset($filters["position"])) {
            $sql .= " AND position LIKE CONCAT( :position, '%')";
            $filter_value["position"] = $filters["position"];
        }
       
        $result["res"] = $this->paginate($sql,$filter_value);
        return $result;
    }
    public function getDraftTeamId($team_id,array $filters): array
    {
        $result = array();
        $sql = "SELECT * FROM draft_history WHERE team_id = :team_id";
        $filter_value = ["team_id" => $team_id];
        //filtering
        if (isset($filters["first_name"])) {
            $sql .= " AND first_name LIKE CONCAT( :first_name, '%')";
            $filter_value["first_name"] = $filters["first_name"];
        }
        if (isset($filters["last_name"])) {
            $sql .= " AND last_name LIKE CONCAT( :last_name, '%')";
            $filter_value["last_name"] = $filters["last_name"];
        }
        if (isset($filters["player_name"])) {
            $sql .= " AND player_name LIKE CONCAT( :player_name, '%')";
            $filter_value["player_name"] = $filters["player_name"];
        }
        if (isset($filters["position"])) {
            $sql .= " AND position LIKE CONCAT( :position, '%')";
            $filter_value["position"] = $filters["position"];
        }
       
        $result["res"] = $this->paginate($sql,$filter_value);
        return $result;
    }
    
}
