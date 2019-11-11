<?php
require 'vendor/autoload.php';

use GuzzleHttp\Client;

class Api
{
    public $baseUrl = 'https://swapi.co/api/';
    public $client;

    function __construct() {
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'http_errors' => false
        ]);
	}

    /**
     * Search for speficif results in api
     */
    public function search(string $name){
        $response = $this->client->get('starships/?search='.$name);
        $body = $response->getBody();
        $stringBody = json_decode($body);
    }

    /**
     * Return all the page results from the api
     */
    public function all(int $pageNumber = 1){
        $statusCode = '200';
        $starships = [];
        while($statusCode == 200){
            $response = $this->client->get('starships/?page='.$pageNumber);
            $statusCode = $response->getStatusCode();
            $body = $response->getBody();
            $results = json_decode($body);
            if(isset($results->results)){
                $starships = array_merge($starships , $results->results);
            }
            $pageNumber++;
        }
        return $starships;
    }
}