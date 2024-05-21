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
    public function parseSports(string $resource_uri): mixed
    {
        $client = new Client($this->client_options);
        $response = $client->get($resource_uri);
        if ($response->getStatusCode() !== 200) {
            return $this->returnError(  
                $response->getStatusCode(),
                $response->getReasonPhrase(),
            );
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
    public function parseShows(string $resource_uri): mixed
    {
        $client = new Client($this->client_options);
        $response = $client->get($resource_uri);
        if ($response->getStatusCode() !== 200) {
            return $this->returnError(
                $response->getStatusCode(),
                $response->getReasonPhrase(),
            );
        }

        $response_data = $response->getBody()->getContents();
        if (empty($response_data)) {
            return $this->returnError(
                "error",
                "Empty response received!",
            );
        }

        $shows = json_decode($response_data, true);
        $parsed_shows = array();
        foreach ($shows as $show) {
            $parsed_show = array(
                "id" => $show['show']['id'],
                "name" => $show['show']['name'],
                "type" => $show['show']['type'],
                "language" => $show['show']['language'],
                "genres" => $show['show']['genres'],
                "status" => $show['show']['status'],
                "runtime" => $show['show']['runtime'],
                "averageRuntime" => $show['show']['averageRuntime'],
                "premiered" => $show['show']['premiered'],
                "summary" => $show['show']['summary']
            );
            $parsed_shows[] = $parsed_show;
        }

        return $parsed_shows;
    }

    private function returnError($code, $message): array
    {
        return array(
            "code" => $code,
            "message" => $message

        );
    }
}
