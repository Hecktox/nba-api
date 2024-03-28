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
        if (isset($filters["active"])){
            if($filters["active"] === 1){
                $sql .= " AND active = 1";
                $filter_values["active"] = $filters["active"];
            } elseif ($filters["active"] === 0){
                $sql .= " AND active = 0";
                $filter_values["active"] = $filters["active"];
            }
        }

        //4. Return an array of the obtained values using the 'paginate' method
        return (array)$this->paginate($sql, $filter_values);
    }

    public function getSinglePlayer($player_id) : mixed
    {
        //1. Create the sql statement to pull the specified player from the db.
        $sql = "SELECT * FROM players WHERE player_id = :player_id ";

        //2. Return the result of the executed statement using the fetchSingle method
        return (array) $this->fetchSingle($sql, ["player_id" => $player_id]);
    }
}
