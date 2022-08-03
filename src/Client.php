<?php

namespace Ushahidi\Platform;

use GuzzleHttp\Client as HttpClient;
use Psr\Http\Message\ResponseInterface;

class Client
{
    private $apiUrl;
    private $apiVersion;
    private $client;

    public function __construct(string $apiUrl, array $options = [], string $version = '5')
    {
        $this->apiUrl = $apiUrl;
        $this->apiVersion = $version;
        $defaultOptions = [
            // Base URI is used with relative requests
            'base_uri' => "$this->apiUrl/api/v$this->apiVersion/",
            // You can set any number of default request options.
            'timeout' => 2.0,
        ];
        $clientOptions = array_merge($defaultOptions, $options);
        $this->client = new HttpClient($clientOptions);
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
     * #INTERIM / #UGLYHACK
     * Submit a post to a survey using the special interim USSD endpoint
     */
    public function createUSSDPost(array $data, string $from, \DateTime $received = null): array
    {
        $url = 'posts/_ussd';

        if (!array_key_exists("source_info", $data)) {
            $data = array_merge($data, [
                "source_info" => [
                    "received" => ($received ?? new \DateTime)->format(\DateTime::ISO8601),
                    "data_source" => "ussd",
                    "type" => "phone",
                    "contact" => $from
                ]
            ]);
        }

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
     * #INTERIM / #UGLYHACK
     * Submit a post to a survey using the special interim WhatsApp endpoint
     */
    public function createWhatsAppPost(array $data, string $from, \DateTime $received = null): array
    {
        $url = 'posts/_whatsapp';

        if (!array_key_exists("source_info", $data)) {
            $data = array_merge($data, [
                "source_info" => [
                    "received" => ($received ?? new \DateTime)->format(\DateTime::ISO8601),
                    "data_source" => "whatsapp",
                    "type" => "phone",
                    "contact" => $from
                ]
            ]);
        }

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

    public function queryLocation(string $query, string $locale = null, $query_id = null, $group_by = null): array
    {
        $url = 'geolocation/query';
        $qs = http_build_query([
            'query' => $query,
            'locale' => $locale,
            'qid' => $query_id,
            'group_by' => $group_by,
        ]);

        return $this->handleResponse($this->client->request(
            'GET',
            "$url?$qs",
            [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
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
