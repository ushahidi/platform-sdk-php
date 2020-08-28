<?php

namespace Ushahidi\Platform;

use GuzzleHttp\Client as HttpClient;
use Psr\Http\Message\ResponseInterface;

class Client
{
    private $apiUrl;
    private $apiVersion;
    private $client;

    public function __construct(string $apiUrl, string $version = '5')
    {
        $this->apiUrl = $apiUrl;
        $this->apiVersion = $version;
        $this->client = new HttpClient([
            // Base URI is used with relative requests
            'base_uri' => "$this->apiUrl/api/v$this->apiVersion/",
            // You can set any number of default request options.
            'timeout' => 2.0,
        ]);
    }

    /**
     * Lists all available surveys from the Ushahidi Platform API.
     */
    public function getAvailableSurveys(): array
    {
        $url = 'surveys?format=minimal';

        return $this->handleResponse($this->client->request(
            'GET',
            $url,
            [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]
        ));
    }

    /**
     * Get one survey from the Ushahidi Platform API.
     * @param int $id
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getSurvey(int $id): array
    {
        $url = 'surveys';

        return $this->handleResponse($this->client->request(
            'GET',
            "$url/$id",
            [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]
        ));
    }

    /**
     * Submit a post to a survey in the Ushahidi Platform API.
     * @param array $data
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createPost(array $data): array
    {
        $url = 'posts';

        return $this->handleResponse($this->client->request(
            'POST',
            "$url",
            [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'json' => $data,
            ]
        ));
    }

    /**
     * @param ResponseInterface $response
     * @return array
     */
    private function handleResponse(ResponseInterface $response): array
    {
        $response_code = $response->getStatusCode();
        $response_reason = $response->getReasonPhrase();
        $body = $response->getBody()->getContents();

        return [
            'body' => json_decode($body, true),
            'status' => $response_code,
            'reason' => $response_reason,
        ];
    }
}
