<?php

namespace Vanier\Api\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Vanier\Api\Exceptions\HttpInvalidInputException;
use Vanier\Api\Helpers\InputsHelper;
use Vanier\Api\Models\PlayersModel;
use Vanier\Api\Exceptions\HttpNoContentException;
use Vanier\Api\Exceptions\HttpInvalidPaginationParameterException;
use Vanier\Api\Validations\Validator;
require_once("validation/validation/Validator.php");

class PlayersController extends BaseController
{
    private $player_model = null;

    public function __construct(){
        $this->player_model = new PlayersModel();
    }


    public function handleGetPlayers(Request $request, Response $response, array $uri_args): Response{
        //1. Retrieve the query params entered by the user
        $filters = $request->getQueryParams();

        //2. Create the pagination logic for the resource
        if(isset($filters["page"]) && isset($filters["page_size"])){
            if(InputsHelper::isInt($filters["page"]) && InputsHelper::isIntAndInRange($filters["page_size"], 1, 20)){
                $this->player_model->setPaginationOptions(
                    $filters["page"],
                    $filters["page_size"]
                );
            } else {
                throw new HttPInvalidPaginationParameterException($request);
        }    }
         else {
                $this->player_model->setPaginationOptions(
                    $filters["page"] = 1,
                    $filters["page_size"] = 5
                );
            }
        
        //!3. Create the sorting logic for the resource


        //4. Pull the list of players from the db
        $players = $this->player_model->getAllPlayers($filters);

        //5. Return a response to the user with the info
        return $this->makeResponse($response, $players);
    }

    public function handleGetPlayer(Request $request, Response $response, array $uri_args): Response {
        //1. Extract the player id from the uri_args array
        $player_id = $uri_args["player_id"];

        //2. Use the id to extract the desired player's info from the db
        $player = $this->player_model->getSinglePlayer($player_id);

        //!3. Throw error if player does not exist
        // if ($player[0] === false) {
        //     //var_dump($player_info);exit;
        //     throw new HttpNoContentException($request);
        // }

        $result = $this->makeResponse($response, $player);

        //4. Return a response to the user
        return $result;
    }

    public function handleGetPlayerDrafts(Request $request, Response $response, array $uri_args): Response {
        
        //1. Retrieve the query params entered by the user
        $filters = $request->getQueryParams();

        //2. Create the pagination logic for the resource
        if(isset($filters["page"]) && isset($filters["page_size"])){
            if(InputsHelper::isInt($filters["page"]) && InputsHelper::isIntAndInRange($filters["page_size"], 1, 20)){
                $this->player_model->setPaginationOptions(
                    $filters["page"],
                    $filters["page_size"]
                );
            } else {
                throw new HttPInvalidPaginationParameterException($request);
        }    }
         else {
                $this->player_model->setPaginationOptions(
                    $filters["page"] = 1,
                    $filters["page_size"] = 5
                );
            }

        //5. Extract the player id from the uri_args array
        $player_id = $uri_args["player_id"];

        //6. Use the id to extract the drafts for the specified player from the db
        $drafts = $this->player_model->getPlayerDrafts($player_id);

        //7. Return a response to the user
        return $this->makeResponse($response, $drafts);
    }

    public function handleCreatePlayers(Request $request, Response $response, array $uri_args): Response {

        //1. Get the information from the body
        $players = $request->getParsedBody();
        
        //2. Validation logic for foreign_key
        // $player_fk = $players[0];

        // $provided_team_id = $player_fk['team_id'];

        // $provided_player_id = $player_fk['person_id'];

        // Validator::addRule(
        //     'invalid_foreign_key',
        //     function($inserted_team_id) use ($provided_team_id){
        //         $result = $this->player_model->verifyTeamId($provided_team_id);

        //         if(empty($result)){
        //             return false;
        //         }

        //         return true;
        //     },
        //     'Foreign key provided team_id does not exists'
        // );

        // Validator::addRule(
        //     'isPresent_player_id',
        //     function($inserted_player_id) use ($provided_player_id){
        //         $result = $this->player_model->verifyPlayerIdPresent($provided_player_id);

        //         if(empty($result)){
        //             return false;
        //         }

        //         return true;
        //     },
        //     'Provided player_id does exists. Unable to add player(s)'
        // );

        $v = new Validator($players);
        $rules = array(
            // 'player_id' => array(
            //     'isPresent_player_id'
            // ),
            'first_name' => array(
                array('regex', '/^[A-Z][a-z]+$/')
            ),
            'last_name' => array(
                array('regex', '/^[A-Z][a-z]+$/')
            ),
            'country' => [
                array('regex', '/^[A-Z][a-z]+$/'),
            ],
            'teamName' => [
                array('regex', '/^[A-Z][a-z]+$/')
            ],
            'team_id' => [
                'integer',
                //'invalid_foreign_key'
            ],
            'draft_number' => [
                array('regex', '/^\d+$/')
            ],
            'from_year' => [
                array('regex', '/^(18[6-9]\d|19\d\d|20[0-1]\d|202[0-4])$/')
            ],
            'to_year' => [
                array('regex', '/^(18[6-9]\d|19\d\d|20[0-1]\d|202[0-4])$/')
            ],
        );

        $v->mapFieldsRules($rules);

        //How to throw appropriate exception
        if($v->validate()){
            foreach($players as $player){
                $this->player_model->createPlayer($player);
            }

            $response_data = array(
                "code" => "success",
                "message" => "The list of players has been created successfully"
            );
    
            return $this->makeResponse($response, $response_data, 201);


        } else {
            print_r($v->errors());
            


        }

        $response_data = array(
            "code" => "failure",
            "message" => "The list of players has not been created."
        );

        return $this->makeResponse($response, $response_data, 500);
    }

    public function handleUpdatePlayers(Request $request, Response $response, array $uri_args): Response{
        $players = $request->getParsedBody();

        $v = new Validator($players);
        $rules = array(
            'fist_name' => array(
                array('regex', '^[A-Z][a-z]+$')
            ),
            'last_name' => array(
                array('regex', '^[A-Z][a-z]+$')
            ),
            'country' => [
                array('regex', '^[A-Z][a-z]+$')
            ],
            'teamName' => [
                array('regex', '^[A-Z][a-z]+$')
            ],
            'team_id' => [
                array('regex', '^\d+$')
            ],
            'draft_number' => [
                array('regex', '^\d+$')
            ],
            'from_year' => [
                array('regex', '^(18[6-9]\d|19\d\d|20[0-1]\d|202[0-4])$')
            ],
            'to_year' => [
                array('regex', '^(18[6-9]\d|19\d\d|20[0-1]\d|202[0-4])$')
            ],
        );

        $v->mapFieldsRules($rules);

        //How to throw appropriate exception
        if($v->validate()){
            foreach ($players as $player){
                $player_id = $player["person_id"];
                unset($player["person_id"]);
                $this->player_model->updatePlayer($player, $player_id);
            }

            $response_data = array(
                "code" => "success",
                "message" => "he specified players have been updated successfully"
            );
    
            return $this->makeResponse($response, $response_data, 201);

        } else {
            print_r($v->errors());
        }



        $response_data = array(
            "code" => "failure",
            "message" => "the specified players have not been updated"
        );

        return $this->makeResponse($response, $response_data, 500);
    }

    public function handleDeletePlayers(Request $request, Response $response, array $uri_args): Response {
        $players = $request->getParsedBody();

        $provided_player_id = $players['person_id'];

        Validator::addRule(
            'invalid_player_id',
            function($inserted_player_id) use ($provided_player_id){
                $result = $this->player_model->verifyPlayerIdAbsent($provided_player_id);

                if(empty($result)){
                    return false;
                }

                return true;
            },
            'Provided player_id does not exist'
        );

         //Check if id exists in the table
         $v = new Validator($players);
         
         $rules = array(
            'player_id' => array(
                'invalid_player_id'
            ),
        );

        $v->mapFieldsRules($rules);

        //How to throw appropriate exception
        if($v->validate()){
            foreach ($players as $player_id){
                $this->player_model->deletePlayer($player_id);
            }
    
            $response_data = array(
                "code" => "success",
                "message" => "the specified players have been deleted successfully"
            );
            return $this->makeResponse($response, $response_data, 201);

        } else {
            print_r($v->errors());
        }


        $response_data = array(
            "code" => "failure",
            "message" => "the specified players have not been deleted"
        );
        return $this->makeResponse($response, $response_data, 500);
    }


}
