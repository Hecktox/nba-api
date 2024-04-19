<?php

namespace Vanier\Api\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Vanier\Api\Helpers\InputsHelper;
use Vanier\Api\Models\PlayersModel;
use Vanier\Api\Exceptions\HttpNoContentException;
use Vanier\Api\Exceptions\HttpInvalidPaginationParameterException;
use Vanier\Api\Validations\Validator;

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

        if ($player[0] === false) {
            //var_dump($player_info);exit;
            throw new HttpNoContentException($request);
        }

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
        $v->rule('required', ['first_name', 'last_name',
         'country', 'position', 'team_id', 'team_name', 'from_year', 'to_year', 'draft_number']);

        


        
        if($v->validate()){
            echo "Yay! All good!";


        } else {
            print_r($v->errors());


        }

        foreach($players as $player){
            $this->player_model->createPlayer($player);
        }



        $response_data = array(
            "code" => "success",
            "message" => "The list of players has been created successfully"
        );

        return $this->makeResponse($response, $response_data, 201);
    }

    public function handleUpdatePlayers(Request $request, Response $response, array $uri_args): Response{
        $players = $request->getParsedBody();

        foreach ($players as $player){
            $player_id = $player["player_id"];
            unset($player["player_id"]);
            $this->player_model->updatePlayer($player, $player_id);
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
