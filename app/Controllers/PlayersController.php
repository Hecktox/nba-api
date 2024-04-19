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
        
        


        //3. Pull the list of players from the db
        $players = $this->player_model->getAllPlayers($filters);

        //4. Return a response to the user with the info
        return $this->makeResponse($response, $players);
    }

    public function handleGetPlayer(Request $request, Response $response, array $uri_args): Response {
        //1. Extract the player id from the uri_args array
        $player_id = $uri_args["player_id"];

        //2. Use the id to extract the desired player's info from the db
        $player = $this->player_model->getSinglePlayer($player_id);

        //var_dump($player);exit;

        // if ($player[0] === false) {
        //     //var_dump($player_info);exit;
        //     throw new HttpNoContentException($request);
        // }

        $result = $this->makeResponse($response, $player);

        //3. Return a response to the user
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

        

        //3. Extract the player id from the uri_args array
        $player_id = $uri_args["player_id"];

        //4. Use the id to extract the drafts for the specified player from the db
        $drafts = $this->player_model->getPlayerDrafts($player_id);

        //5. Return a response to the user
        return $this->makeResponse($response, $drafts);


    }

    public function handleCreatePlayers(Request $request, Response $response, array $uri_args): Response {
        $players = $request->getParsedBody();

        $v = new Validator($players);
        $rules = array(
            'fist_name' => array(
                'required',
                array('regex', '^[A-Z][a-z]+$')
            ),
            'last_name' => array(
                'required',
                array('regex', '^[A-Z][a-z]+$')
            ),
            'country' => [
                'required',
                array('regex', '^[A-Z][a-z]+$')
            ],
            'teamName' => [
                'required',
                array('regex', '^[A-Z][a-z]+$')
            ],
            'team_id' => [
                'required',
                array('regex', '^\d+$')
            ],
            'draft_number' => [
                'required',
                array('regex', '^\d+$')
            ],
            'from_year' => [
                'required',
                array('regex', '^(18[6-9]\d|19\d\d|20[0-1]\d|202[0-4])$')
            ],
            'to_year' => [
                'required',
                array('regex', '^(18[6-9]\d|19\d\d|20[0-1]\d|202[0-4])$')
            ],
        );

        $v->mapFieldsRules($rules);

        //How to throw appropriate exception
        if($v->validate()){
            foreach($players as $player){
                $this->player_model->createPlayer($player);
            }
        } else {
            throw new HttpInvalidPaginationParameterException($request);
        }

        $response_data = array(
            "code" => "success",
            "message" => "The list of players has been created successfully"
        );

        return $this->makeResponse($response, $response_data, 201);
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
                $player_id = $player["player_id"];
                unset($player["player_id"]);
                $this->player_model->updatePlayer($player, $player_id);
            }
        } else {
            print_r($v->errors());
        }



        $response_data = array(
            "code" => "success",
            "message" => "he specified players have been updated successfully"
        );

        return $this->makeResponse($response, $response_data, 201);
    }

    public function handleDeletePlayers(Request $request, Response $response, array $uri_args): Response {
        $players = $request->getParsedBody();

         //Check if id exists in the table
         $v = new Validator($players);
         $v->rule(function($field, $value, $params, $fields) {

             return true;
         }, "")->message("{field} failed...");

        foreach ($players as $player_id){
            $this->player_model->deletePlayer($player_id);
        }

        $response_data = array(
            "code" => "success",
            "message" => "he specified players have been deleted successfully"
        );
        return $this->makeResponse($response, $response_data, 201);
    }


}
