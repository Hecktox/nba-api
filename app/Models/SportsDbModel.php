<?php
namespace Vanier\Api\Models;

use GuzzleHttp\Client;

class SportsDbModel extends BaseModel
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function searchLeagues(string $sport = 'Basketball', string $country = '', string $order = 'asc'): array
    {
        try {
            $queryParams = [];
            $queryParams['s'] = $sport;
            if (!empty($country)) {
                $queryParams['c'] = $country;
            }
            $queryString = http_build_query($queryParams);
            $url = "https://www.thesportsdb.com/api/v1/json/3/search_all_leagues.php?$queryString";

            $response = $this->client->get($url);

            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Invalid response from the API');
            }

            $apiData = json_decode($response->getBody()->getContents(), true);
            return $apiData;
        } catch (\Exception $e) {
            throw new \Exception('Failed to retrieve data from the API: ' . $e->getMessage());
        }
    }
}
