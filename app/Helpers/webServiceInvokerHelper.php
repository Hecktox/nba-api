<?php

namespace Vanier\Api\Helpers;

use GuzzleHttp\Client;

class webServiceInvokerHelper
{
    private array $client_options = [];

    public function __construct($options = [])
    {
        $this->client_options = $options;
    }
    public function invokeURI(string $resource_uri): mixed
    {
        $client = new Client($this->client_options);
        $response = $client->get($resource_uri);
        if ($response->getStatusCode() !== 200) {
            return $this->returnError(
                $response->getStatusCode(),
                $response->getReasonPhrase(),
            );
            return false;
        }
        $response_data = $response->getBody()->getContents();
        if (empty($response_data)) {
            return $this->returnError(
                "error",
                "Empty response received!",
            );
        }
        $leagues = json_decode($response_data);
        $parsed_leagues = array();
        foreach ($leagues->countries as $key => $league) {
            $parsed_leagues[$key]["idLeague"] = $league->idLeague;
            $parsed_leagues[$key]["strSport"] = $league->strSport;
            $parsed_leagues[$key]["strLeague"] = $league->strLeague;
            $parsed_leagues[$key]["strCountry"] = $league->strCountry;
            $parsed_leagues[$key]["strGender"] = $league->strGender;
            $parsed_leagues[$key]["strCurrentSeason"] = $league->strCurrentSeason;
            $parsed_leagues[$key]["intFormedYear"] = $league->intFormedYear;
            $parsed_leagues[$key]["strWebsite"] = $league->strWebsite;
            $parsed_leagues[$key]["strDescriptionEN"] = $league->strDescriptionEN;
        }
        return $parsed_leagues;
    }

    private function returnError($code, $message): array
    {
        return array(
            "code" => $code,
            "message" => $message

        );
    }
}
