<?php

namespace Vanier\Api\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Vanier\Api\Helpers\webServiceInvokerHelper;
use Vanier\Api\Models\DraftModel;
use Vanier\Api\Exceptions\HttpInvalidInputException;
use Vanier\Api\Exceptions\HttpInvalidPaginationParameterException;
use Vanier\Api\Validations\Validator;

require_once("validation/validation/Validator.php");

class DraftController extends BaseController
{
    private $draft_model = null;

    public function __construct()
    {
        $this->draft_model = new DraftModel;
    }

    private function assertDraftId($request, $draft_id)
    {
        if (strlen($draft_id) !== 10) {
            throw new HttpInvalidPaginationParameterException($request, "Invalid team ID format. Must be a 10-character string.");
        }
    }

    public function handleGetDraft(Request $request, Response $response, array $uri_args): Response
    {
        $filters = $request->getQueryParams();
        $page = $filters["page"] ?? 1;
        $page_size = $filters["page_size"] ?? 10;
        
        if (!is_numeric($page) || $page < 1) {
            throw new HttpInvalidPaginationParameterException($request, "Invalid page number. Must be a positive integer.");
        }
        if (!is_numeric($page_size) || $page_size < 1 || $page_size > 50) {
            throw new HttpInvalidPaginationParameterException($request, "Invalid page size. Must be an integer between 1 and 50.");
        }
        $this->draft_model->setPaginationOptions($page, $page_size);
        $teams = $this->draft_model->getAllDraftStats($filters);

        return $this->makeResponse($response, $teams);
    }

    public function handleGetDraftPlayerId(Request $request, Response $response, array $uri_args): Response
    {
        $player_id = $uri_args["player_id"];
        $draft_info = $this->draft_model->getDraftPlayerId($player_id);

        if (empty($draft_info)) {
            throw new HttpInvalidInputException(
                $request,
                "The supplied draft ID is not valid"
            );
        }

        return $this->makeResponse($response, $draft_info);
    }

    public function handleGetPlayerIdSeason(Request $request, Response $response, array $uri_args): Response
    {
        $team_id = $uri_args["player_id"];
        $draft_info = $this->draft_model->getDraftPlayerIdSeason($team_id);

        if (empty($draft_info)) {
            throw new HttpInvalidInputException(
                $request,
                "The supplied draft ID is not valid"
            );
        }

        return $this->makeResponse($response, $draft_info);
    }

    public function handleCreateDraft(Request $request, Response $response, array $uri_args): Response
    {
        $drafts = $request->getParsedBody();
        $v = new Validator($drafts);
        $rules = array(
            'season' => array(
                array('regex', '/^(18[6-9]\d|19\d\d|20[0-1]\d|202[0-4])$/')
            ),
            'player_id' => array(
                'required'
            ),
            'first_name' => array(
                array('regex', '/^[A-Z][a-z]+$/')
            ),
            'last_name' => array(
                array('regex', '/^[A-Z][a-z]+$/')
            ),
            'player_name' => [
                array('regex', '/^[A-Z][a-z]+(?: [A-Z][a-z]+)*$/'),
            ],
            'position' => [
                array('regex', '/^(PG|SG|SF|PF|C)$/')
            ],
            'wingspan' => [
                array('regex', '/^\d+(\.\d+)?$/')
            ],
            'standing_reach' => [
                array('regex', '/^\d+(\.\d+)?$/')
            ],
            'hand_lenght' => [
                array('regex', '/^\d+(\.\d+)?$/')
            ],
            'hand_widht' => [
                array('regex', '/^\d+(\.\d+)?$/')
            ],
            'standing_vertical_leap' => [
                array('regex', '/^\d+(\.\d+)?$/')
            ],
            'Max_vertical_leap' => [
                array('regex', '/^\d+(\.\d+)?$/')
            ],
            'bench_press' => [
                array('regex', '/^\d+(\.\d+)?$/')
            ],
        );

        $v->mapFieldsRules($rules);

        //How to throw appropriate exception
        if($v->validate()){
            foreach($drafts as $draft){
                $this->draft_model->createDraft($draft); // Pass each $draft individually
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

    public function handleUpdateDraft(Request $request, Response $response, array $uri_args): Response
    {
        $drafts = $request->getParsedBody();

        $v = new Validator($drafts);
        $rules = array(
            'season' => array(
                array('regex', '/^(18[6-9]\d|19\d\d|20[0-1]\d|202[0-4])$/')
            ),
            'first_name' => array(
                array('regex', '/^[A-Z][a-z]+$/')
            ),
            'last_name' => array(
                array('regex', '/^[A-Z][a-z]+$/')
            ),
            'player_name' => [
                array('regex', '/^[A-Z][a-z]+$/'),
            ],
            'position' => [
                array('regex', '/^(PG|SG|SF|PF|C)$/')
            ],
            'wingspan' => [
                array('regex', '/^\d+(\.\d+)?$/')
            ],
            'standing_reach' => [
                array('regex', '/^\d+(\.\d+)?$/')
            ],
            'hand_lenght' => [
                array('regex', '/^\d+(\.\d+)?$/')
            ],
            'hand_widht' => [
                array('regex', '/^\d+(\.\d+)?$/')
            ],
            'standing_vertical_leap' => [
                array('regex', '/^\d+(\.\d+)?$/')
            ],
            'Max_vertical_leap' => [
                array('regex', '/^\d+(\.\d+)?$/')
            ],
            'bench_press' => [
                array('regex', '/^\d+(\.\d+)?$/')
            ],
        );

        $v->mapFieldsRules($rules);

        //How to throw appropriate exception
        if($v->validate()){
            foreach ($drafts as $draft){
                $player_id = $draft["player_id"];
                unset($player["player_id"]);
                $this->draft_model->updateDraft($draft, $player_id);
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

    public function handleDeleteDraft(Request $request, Response $response, array $uri_args): Response
    {
        $drafts = $request->getParsedBody();


         //Check if id exists in the table
         $v = new Validator($drafts);
         
         $rules = array(
            'player_id' => array(
                'required'
            ),
        );

        $v->mapFieldsRules($rules);

        //How to throw appropriate exception
        if($v->validate()){
            foreach ($drafts as $player_id){
                $this->draft_model->deleteDraft($player_id);
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
