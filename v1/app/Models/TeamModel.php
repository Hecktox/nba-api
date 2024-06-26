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

    public function getTeamHistory($team_id, array $filters = []): array
    {
        $result = [];
        $result["team"] = $this->getTeamInfo($team_id);

        $sql = "SELECT * FROM team_history WHERE team_id = :team_id";
        $filter_values = ["team_id" => $team_id];

        if (isset($filters["match_result"])) {
            $sql .= " AND match_result LIKE CONCAT(:match_result, '%')";
            $filter_values["match_result"] = $filters["match_result"];
        }

        $history = $this->fetchAll($sql, $filter_values);

        $result["history"] = $history;
        return $result;
    }

    public function createTeam(array $team_data): bool
    {
        try {
            $result = $this->insert("team", $team_data);
            if ($result === false) {
                error_log("Database insert failed for team: " . json_encode($team_data));
            }
            return $result !== false;
        } catch (\Exception $e) {
            error_log("Exception during team creation: " . $e->getMessage());
            return false;
        }
    }


    public function updateTeam(array $team_data, int $team_id): mixed
    {
        return $this->update("team", $team_data, ["team_id" => $team_id]);
    }

    public function deleteTeam(int $team_id): mixed
    {
        return $this->delete("team", ["team_id" => $team_id]);
    }

    public function verifyTeamId(string $team_id): bool
    {
        $sql = "SELECT COUNT(*) AS count FROM team WHERE team_id = :team_id";
        $result = $this->fetchSingle($sql, ["team_id" => $team_id]);
        return $result['count'] > 0;
    }
}
