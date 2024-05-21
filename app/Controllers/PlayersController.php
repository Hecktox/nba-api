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

    /**
     * 
     * Handles the GET /players route of the app route.
     */
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

        /**
     * 
     * Handles the GET /player route of the app route.
     */
    public function handleGetPlayer(Request $request, Response $response, array $uri_args): Response {
        //1. Extract the player id from the uri_args array
        $player_id = $uri_args["player_id"];

        //2. Use the id to extract the desired player's info from the db
        $player = $this->player_model->getSinglePlayer($player_id);

        //!3. Throw error if player does not exist
        // if ($player === false) {
        //     //var_dump($player_info);exit;
        //     throw new HttpNoContentException($request);
        // }

        $result = $this->makeResponse($response, $player);

        //4. Return a response to the user
        return $result;
    }

    /**
     * 
     * Handles the GET /teams route of the app route.
     * NOTE: Documentation is pretty similar for all other controller and model classes
     * so only the 'team' route will be documented to minimize redundancy.
     */
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

    /**
     * 
     * Handles the POST /players route of the app route.
     */
    public function handleCreatePlayers(Request $request, Response $response, array $uri_args): Response {

        //1. Get the information from the body
        $players = $request->getParsedBody();
        
        if (empty($players)) {
            $response_data = array(
                "code" => "error",
                "message" => "Empty request body"
            );
            return $this->makeResponse($response, $response_data, 400); // 400 Bad Request status code
        }


        $v = new Validator($players);
        $rules = array(
            'person_id' => array(
                'required'
            ),
            'first_name' => array(
                array('regex', '/^[A-Z][a-z]+$/')
            ),
            'last_name' => array(
                array('regex', '/^[A-Z][a-z]+$/')
            ),
            'birthdate' => array(
                array('regex', '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/')
            ),
            'school' => [
                array('regex', '/^[A-Z][a-z]+$/'),
            ],
            'height' => [
                array('regex', '/^\d+-\d+$/')
            ],
            'weight' => [
                array('regex', '/^\d+$/')
            ],
            'jersey' => [
                array('regex', '/^\d+$/')
            ],
            'team_abbreviation' => [
                array('regex', '/^[A-Z]{3}$/')
            ],
            'team_code' => [
                array('regex', '/^[a-z]+$/')
            ],
            'team_city' => [
                array('regex', '/^[A-Z][a-zA-Z\s]+$/')
            ],
            'draft_year' => [
                array('regex', '/^\d{4}$/')
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


        if($v->validate()){
            foreach($players as $player){
                if ($this->player_model->verifyPlayerId($player['person_id'])){
                    $response_data = array(
                        "code" => "failure",
                        "message" => "Player with given ID already exists"
                    );
                    return $this->makeResponse(
                        $response, $response_data, 400
                    );
                } else {
                    $this->player_model->createPlayer($player);
                }
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

        return $this->makeResponse($response, $response_data, 400);
    }

    /**
     * 
     * Handles the PUT /players route of the app route.
     */
    public function handleUpdatePlayers(Request $request, Response $response, array $uri_args): Response{
        $players = $request->getParsedBody();

        $v = new Validator($players);
        $rules = array(
            'person_id' => array(
                'required'
            ),
            'first_name' => array(
                array('regex', '/^[A-Z][a-z]+$/')
            ),
            'last_name' => array(
                array('regex', '/^[A-Z][a-z]+$/')
            ),
            'birthdate' => array(
                array('regex', '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/')
            ),
            'school' => [
                array('regex', '/^[A-Z][a-z]+$/'),
            ],
            'height' => [
                array('regex', '/^\d+-\d+$/')
            ],
            'weight' => [
                array('regex', '/^\d+$/')
            ],
            'jersey' => [
                array('regex', '/^\d+$/')
            ],
            'team_abbreviation' => [
                array('regex', '/^[A-Z]{3}$/')
            ],
            'team_code' => [
                array('regex', '/^[a-z]+$/')
            ],
            'team_city' => [
                array('regex', '/^[A-Z][a-zA-Z\s]+$/')
            ],
            'draft_year' => [
                array('regex', '/^\d{4}$/')
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

        return $this->makeResponse($response, $response_data, 400);
    }

    /**
     * 
     * Handles the DELETE /players route of the app route.
     */
    public function handleDeletePlayers(Request $request, Response $response, array $uri_args): Response {
        $players = $request->getParsedBody();


         //Check if id exists in the table
         $v = new Validator($players);
         
         $rules = array(
            'player_id' => array(
                'required'
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
        return $this->makeResponse($response, $response_data, 400);
    }


}
