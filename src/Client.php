<?php


namespace PlatformSDK;
use GuzzleHttp\Client;

class Ushahidi
{
    private $survey;
    private $availableSurveys;
    private $apiUrl;
    private $apiVersion = "5";

    public function __construct(string $apiUrl, $version = "5")
    {
        $this->apiUrl = $apiUrl;
        $this->apiVersion = $version;

        $client = new GuzzleHttp\Client([
            // Base URI is used with relative requests
            'base_uri' => "$this->apiUrl/api/v$this->apiVersion",
            // You can set any number of default request options.
            'timeout'  => 2.0,
        ]);
    }

    /**
     * Lists all available surveys from the Ushahidi Platform API
     * @return array
     */
    public function getAvailableSurveys()
    {
        $url = "/surveys?format=minimal";
        return $this->client->request(
            'GET',
            $url,
            [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json'
            ]
        );
    }

    /**
     * Get one survey from the Ushahidi Platform API
     * @return array
     */
    public function getSurvey(int $id)
    {
        $url = "/surveys";
        return $this->client->request(
            'GET',
            "$url/$id",
            [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json'
            ]
        );
    }

    /**
     * Submit a post to a survey in the Ushahidi Platform API
     * @return array
     */
    public function createPost(array $data)
    {
        $url = "/posts";
        return $this->client->request(
            'POST',
            "$url",
            [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json'
            ]
        );
    }
}
