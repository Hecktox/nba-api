<?php

namespace Vanier\Api\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Vanier\Api\Models\DraftModel;
use Vanier\Api\Exceptions\HttpInvalidInputException;
use Vanier\Api\Exceptions\HttpInvalidPaginationParameterException;
use Vanier\Api\Validations\Validator;
class DraftController extends BaseController
{
    private $draft_model = null;
    public function __construct()
    {
        $this->draft_model= new DraftModel;
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
        $draft = $request->getParsedBody();
        $v = new Validator($draft);
        $rules = array(
            'season' => [
                array('regex', '\b\d{1,4}\b')
            ],
            'player_id' => [
                'integer'
            ],
            'first_name' => [
                array('regex', '^[A-Z][a-z]+$')
            ],
            'last_name' => [
                array('regex', '^[A-Z][a-z]+$')
            ],
            'player_name' => [
                array('regex', '^[A-Z][a-z]+$')
            ],
            'position' => [
                array('regex', '^\b(PG|SG|SF|PF|C)\b$')
            ],
            'weight' => [
                array('regex', '/^\d{1,3}(?:[.]\d+)?$/gm')
            ],
            'wingspan' => [
                array('regex', '/^\d{1,3}(?:[.]\d+)?$/gm')
            ],
            'standing_reach' => [
                array('regex', '/^\d{1,3}(?:[.]\d+)?$/gm')
            ],
            'hand_length' => [
                array('regex', '/^\d{1,3}(?:[.]\d+)?$/gm')
            ],
            'hand_width' => [
                array('regex', '/^\d{1,3}(?:[.]\d+)?$/gm')
            ],
            'standing_vertical_leap' => [
                array('regex', '/^\d{1,3}(?:[.]\d+)?$/gm')
            ],
            'max_vertical_leap' => [
                array('regex', '/^\d{1,3}(?:[.]\d+)?$/gm')
            ],
            'bench_press' => [
                array('regex', '/^\d{1,3}(?:[.]\d+)?$/gm')
            ],
        );

        $v->mapFieldsRules($rules);

        if ($v->validate()) {
            foreach ($draft as $draft) {
                $this->draft_model->createDraft($draft);
            }

        }
        foreach ($draft as $game) {
            $this->draft_model->createDraft($game);
        }

        $response_data = array(
            "code" => "success",
            "message" => "The list of games has been created successfully"
        );

        return $this->makeResponse($response, $response_data, 201);
    }

    public function handleUpdateDraft(Request $request, Response $response, array $uri_args): Response
    {
        $draft = $request->getParsedBody();
        $v = new Validator($draft);
        $rules = array(
            'season' => [
                array('regex', '\b\d{1,4}\b')
            ],
            'player_id' => [
                'integer'
            ],
            'first_name' => [
                array('regex', '^[A-Z][a-z]+$')
            ],
            'last_name' => [
                array('regex', '^[A-Z][a-z]+$')
            ],
            'player_name' => [
                array('regex', '^[A-Z][a-z]+$')
            ],
            'position' => [
                array('regex', '^\b(PG|SG|SF|PF|C)\b$')
            ],
            'weight' => [
                array('regex', '/^\d{1,3}(?:[.]\d+)?$/gm')
            ],
            'wingspan' => [
                array('regex', '/^\d{1,3}(?:[.]\d+)?$/gm')
            ],
            'standing_reach' => [
                array('regex', '/^\d{1,3}(?:[.]\d+)?$/gm')
            ],
            'hand_length' => [
                array('regex', '/^\d{1,3}(?:[.]\d+)?$/gm')
            ],
            'hand_width' => [
                array('regex', '/^\d{1,3}(?:[.]\d+)?$/gm')
            ],
            'standing_vertical_leap' => [
                array('regex', '/^\d{1,3}(?:[.]\d+)?$/gm')
            ],
            'max_vertical_leap' => [
                array('regex', '/^\d{1,3}(?:[.]\d+)?$/gm')
            ],
            'bench_press' => [
                array('regex', '/^\d{1,3}(?:[.]\d+)?$/gm')
            ],
        );

        $v->mapFieldsRules($rules);

        if ($v->validate()) {
            foreach ($draft as $draft) {
                $this->draft_model->createDraft($draft);
            }

        }
        foreach ($draft as $game) {
            $this->draft_model->createDraft($game);
        }
        foreach ($draft as $draft) {
            $player_id = $draft["player_id"];
            unset($draft["player_id"]);
            $this->draft_model->updateDraft($draft, $player_id);
        }

        $response_data = array(
            "code" => "success",
            "message" => "The specified games have been updated successfully"
        );

        return $this->makeResponse($response, $response_data, 201);
    }

    public function handleDeleteDraft(Request $request, Response $response, array $uri_args): Response
    {
        $draft = $request->getParsedBody();
        $v = new Validator($draft);
        $rules = array(
            'season' => [
                array('regex', '\b\d{1,4}\b')
            ],
            'player_id' => [
                'integer'
            ],
            'first_name' => [
                array('regex', '^[A-Z][a-z]+$')
            ],
            'last_name' => [
                array('regex', '^[A-Z][a-z]+$')
            ],
            'player_name' => [
                array('regex', '^[A-Z][a-z]+$')
            ],
            'position' => [
                array('regex', '^\b(PG|SG|SF|PF|C)\b$')
            ],
            'weight' => [
                array('regex', '/^\d{1,3}(?:[.]\d+)?$/gm')
            ],
            'wingspan' => [
                array('regex', '/^\d{1,3}(?:[.]\d+)?$/gm')
            ],
            'standing_reach' => [
                array('regex', '/^\d{1,3}(?:[.]\d+)?$/gm')
            ],
            'hand_length' => [
                array('regex', '/^\d{1,3}(?:[.]\d+)?$/gm')
            ],
            'hand_width' => [
                array('regex', '/^\d{1,3}(?:[.]\d+)?$/gm')
            ],
            'standing_vertical_leap' => [
                array('regex', '/^\d{1,3}(?:[.]\d+)?$/gm')
            ],
            'max_vertical_leap' => [
                array('regex', '/^\d{1,3}(?:[.]\d+)?$/gm')
            ],
            'bench_press' => [
                array('regex', '/^\d{1,3}(?:[.]\d+)?$/gm')
            ],
        );

        $v->mapFieldsRules($rules);

        if ($v->validate()) {
            foreach ($draft as $draft) {
                $this->draft_model->createDraft($draft);
            }

        }
        foreach ($draft as $game) {
            $this->draft_model->createDraft($game);
        }
        foreach ($draft as $player_id) {
            $this->draft_model->deleteDraft($player_id);
        }

        $response_data = array(
            "code" => "success",
            "message" => "The specified games have been deleted successfully"
        );

        return $this->makeResponse($response, $response_data, 201);
    }
}
