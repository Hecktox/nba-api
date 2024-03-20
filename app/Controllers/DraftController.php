<?php

namespace Vanier\Api\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Vanier\Api\Models\DraftModel;
use Vanier\Api\Exceptions\HttpInvalidInputExecption;
class DraftController extends BaseController
{
    private $draft_model = null;
    public function __construct()
    {
        $this->draft_model= new DraftModel;
    }
    public function handleGetDraft(Request $request, Response $response, array $uri_args): Response
    {
        //we get the values to after pass it to the model and do pagination
        $filters = $request->getQueryParams();

        //TODO: validate the paginaton params
        $this->draft_model->setPaginationOptions(
            $filters["page"] ?? 4,
            $filters["page_size"] ?? 10,
        );
        $drafts = $this->draft_model->getAllDraftStats($filters);

        return $this->makeResponse($response, $drafts);
    }
    public function handleGetDraftPersonId(Request $request, Response $response, array $uri_args): Response
    {
        $filters = $request->getQueryParams();

         //TODO: validate the paginaton params
         $this->draft_model->setPaginationOptions(
             $filters["page"] ?? 4,
             $filters["page_size"] ?? 10,
         );
         $person_id = $uri_args["person_id"];
         $team = $this->draft_model->getDraftPersonId($person_id,$filters);
 
         return $this->makeResponse($response, $team);
		 
		 
    }
    public function handleGetDraftTeamId(Request $request, Response $response, array $uri_args): Response
    {
        $filters = $request->getQueryParams();

         //TODO: validate the paginaton params
         $this->draft_model->setPaginationOptions(
             $filters["page"] ?? 4,
             $filters["page_size"] ?? 10,
         );
         $team_id = $uri_args["team_id"];
         $team = $this->draft_model->getDraftTeamId($team_id,$filters);
 
         return $this->makeResponse($response, $team);
		 
		 
    }
}
