<?php

namespace Vanier\Api\Models;

class PlayersModel extends BaseModel
{
    public function __construct(){
        parent::__construct();
    }

    public function getAllPlayers(array $filters) : array{
        //1. Create a local array to hold the values of the filters
        $filter_values = [];

        //2. Create the sql statement to pull all players from the db
        $sql = "SELECT * from common_player_info WHERE 1";



        //3. Create the filter statements

        if (isset($filters["first_name"])){
            $sql .= " AND first_name LIKE CONCAT(:first_name, '%') ";
            $filter_values["first_name"] = $filters["first_name"];
        }

        if (isset($filters["country"])){
            $sql .= " AND country LIKE CONCAT(:country, '%') ";
            $filter_values["country"] = $filters["country"];
        }

        if (isset($filters["birthdate"])){
            $sql .= " AND birthdate > :b_date";
            $filter_values["b_date"] = $filters["birthdate"];
        }

        if (isset($filters["team_name"])){
            $sql .= " AND team_name LIKE CONCAT(:team_name, '%') ";
            $filter_values["team_name"] = $filters["team_name"];
        }

        //4. Return an array of the obtained values using the 'paginate' method
        return (array)$this->paginate($sql, $filter_values);
    }

    public function getSinglePlayer(string $person_id) : mixed
    {
        //1. Create the sql statement to pull the specified player from the db.
        $sql = "SELECT * FROM common_player_info WHERE person_id = :person_id ";

        //2. Return the result of the executed statement using the fetchSingle method
        return (array) $this->fetchSingle($sql, ["person_id" => $person_id]);
    }

    public function getPlayerDrafts(string $person_id) : mixed {
        //1. Create an array for the result of the query and for the filter values
        $result = array();

        //2. Get the specified player's info
        $result["player"] = $this->getSinglePlayer($person_id);

        //3. Get the list of drafts for the specified player
        $sql = "SELECT * FROM draft_history WHERE person_id = :person_id";
        $drafts = $this->paginate($sql, ["person_id" => $person_id]);

        $result["drafts"] = $drafts;

        return $result;
    }
}
