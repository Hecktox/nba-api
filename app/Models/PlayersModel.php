<?php

namespace Vanier\Api\Models;

class PlayersModel extends BaseModel
{
    public function __construct(){
        parent::__construct();
    }

    
    /**
     * 
     * Retrieves all the players inside of the database and processes 
     * the appropriate filters specified by the user.
     * 
     * 
     */
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

        if (isset($filters["last_name"])){
            $sql .= " AND last_name LIKE CONCAT(:last_name, '%') ";
            $filter_values["last_name"] = $filters["last_name"];
        }

        if (isset($filters["birthdate"])){
            $sql .= " AND birthdate > :b_date";
            $filter_values["b_date"] = $filters["birthdate"];
        }

        if (isset($filters["school"])){
            $sql .= " AND school LIKE CONCAT(:school, '%') ";
            $filter_values["school"] = $filters["school"];
        }

        if (isset($filters["height"])){
            $sql .= " AND height LIKE CONCAT(:height, '%') ";
            $filter_values["height"] = $filters["height"];
        }

        if (isset($filters["weight"])){
            $sql .= " AND weight LIKE CONCAT(:weight, '%') ";
            $filter_values["weight"] = $filters["weight"];
        }

        if (isset($filters["jersey"])){
            $sql .= " AND jersey LIKE CONCAT(:jersey, '%') ";
            $filter_values["jersey"] = $filters["jersey"];
        }

        if (isset($filters["team_abbreviation"])){
            $sql .= " AND team_abbreviation LIKE CONCAT(:team_abbreviation, '%') ";
            $filter_values["team_abbreviation"] = $filters["team_abbreviation"];
        }

        if (isset($filters["team_code"])){
            $sql .= " AND team_code LIKE CONCAT(:team_code, '%') ";
            $filter_values["team_code"] = $filters["team_code"];
        }

        if (isset($filters["team_city"])){
            $sql .= " AND team_city LIKE CONCAT(:team_city, '%') ";
            $filter_values["team_city"] = $filters["team_city"];
        }

        if (isset($filters["draft_year"])){
            $sql .= " AND draft_year LIKE CONCAT(:draft_year, '%') ";
            $filter_values["draft_year"] = $filters["draft_year"];
        }

        if (isset($filters["country"])){
            $sql .= " AND country LIKE CONCAT(:country, '%') ";
            $filter_values["country"] = $filters["country"];
        }

        if (isset($filters["team_name"])){
            $sql .= " AND team_name LIKE CONCAT(:team_name, '%') ";
            $filter_values["team_name"] = $filters["team_name"];
        }

        if (isset($filters["team_id"])){
            $sql .= " AND team_id LIKE CONCAT(:team_id, '%') ";
            $filter_values["team_id"] = $filters["team_id"];
        }

        if (isset($filters["draft_number"])){
            $sql .= " AND draft_number LIKE CONCAT(:draft_number, '%') ";
            $filter_values["draft_number"] = $filters["draft_number"];
        }

        if (isset($filters["from_year"])){
            $sql .= " AND from_year > :from_year";
            $filter_values["from_year"] = $filters["from_year"];
        }

        if (isset($filters["to_year"])){
            $sql .= " AND to_year > :to_year";
            $filter_values["to_year"] = $filters["to_year"];
        }

        if (isset($filters["order"]) && in_array($filters["order"], ["asc", "desc"])) {
            $sql .= " ORDER BY last_name " . strtoupper($filters["order"]);
        }

        //4. Return an array of the obtained values using the 'paginate' method
        return (array)$this->paginate($sql, $filter_values);
    }

    
    /**
     * 
     * Retrieves a single player from the database using the person_id specified by the
     * user.
     * 
     * 
     */
    public function getSinglePlayer(string $person_id) : mixed
    {
        //1. Create the sql statement to pull the specified player from the db.
        $sql = "SELECT * FROM common_player_info WHERE person_id = :person_id ";

        //2. Return the result of the executed statement using the fetchSingle method
        return (array) $this->fetchSingle($sql, ["person_id" => $person_id]);
    }

    
    /**
     * 
     * Verifies if the specified team_id foreign key exists in the team table.
     * 
     * 
     */

    public function verifyTeamId($team_id): mixed {
        $sql = "SELECT * FROM team WHERE team_id = :team_id";

        // var_dump($sql);
        // exit;
        //!Statement executes with an error
        $returnValue = (array) $this->fetchSingle($sql, ["team_id" => $team_id]);
        
        //  var_dump($returnValue);
        //  exit;

        if($returnValue == False){
            return false;
        } 
        return true;
    }

    public function getPlayerDrafts(string $player_id) : mixed {
        //1. Create an array for the result of the query and for the filter values
        $result = array();

        //2. Get the specified player's info
        $result["player"] = $this->getSinglePlayer($player_id);

        //3. Get the list of drafts for the specified player
        $sql = "SELECT * FROM draft_combine_stats WHERE player_id = :player_id";
        $drafts = $this->paginate($sql, ["player_id" => $player_id]);

        $result["drafts"] = $drafts;

        return $result;
    }

    
    /**
     * 
     * Add a player instance 
     * 
     * 
     */

    public function createPlayer(array $player_data): mixed {
        return $this->insert("common_player_info", $player_data);
    }

    public function updatePlayer(array $player_data, int $player_id): mixed{
        return $this->update("common_player_info", $player_data, ["person_id" => $player_id]);
    }

    public function deletePlayer(int $player_id): mixed {
        return $this->delete("common_player_info", ["person_id" => $player_id]);
    }

    public function verifyPlayerId(string $person_id): bool
    {
        $sql = "SELECT COUNT(*) AS count FROM common_player_info WHERE person_id = :person_id";
        $result = $this->fetchSingle($sql, ["person_id" => $person_id]);
        return $result['count'] > 0;
    }
}
