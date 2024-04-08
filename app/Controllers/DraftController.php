<?php

namespace Vanier\Api\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Vanier\Api\Models\DraftModel;
use Vanier\Api\Exceptions\HttpInvalidInputException;
use Vanier\Api\Exceptions\HttpInvalidPaginationParameterException;
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
}
