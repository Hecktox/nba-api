<?php

namespace Vanier\Api\Helpers;
use GuzzleHttp\Client;

class webServiceInvokerHelper
{
    private array $client_options = [];

public function __construct($options = []) {
    //TODO: you can past a list of options if you customize the Guzzle client such as base URI time out...
    $this->client_options = $options;
}
public function invokeURI(string $resource_uri) :mixed{
    // We will initiate a GET Request
    $client = new Client($this->client_options);
    $response = $client->get($resource_uri);
    //TODO: ! We need to process the response
    //? Validate the response check the status code, response header: Content-type: application/json
    if($response->getStatusCode()!== 200){
        //Or you can return an array containing: the code message/reason.
        return $this->returnError(
            $response->getStatusCode(),
            $response->getReasonPhrase(),
        );
        return false;
    }
    //We have a Valid response ==> process it.
    //prepare the data structure to be parsed:
    $response_data = $response->getBody()->getContents();
    if(empty($response_data)){
        return $this->returnError(
            "error",
            "Empty response recived!",
        );
    }
    $leagues = json_decode($response_data);
    $parsed_leagues = array();
    foreach ($leagues->countries as $key => $league) {
        $parsed_leagues[$key]["IdLeague"] = $league->idLeague;
        $parsed_leagues[$key]["strSport"] = $league->strSport;
        $parsed_leagues[$key]["strLeague"] = $league->strLeague;
        $parsed_leagues[$key]["strCountry"] = $league->strCountry;
        $parsed_leagues[$key]["strGender"] = $league->strGender;
        $parsed_leagues[$key]["strCurrentSeason"] = $league->strCurrentSeason;
    }
    return $parsed_leagues;
}

private function returnError($code,$message) : array{
    return array(
        "code"=> $code,
        "message"=> $message

    );
}


}
