<?php

namespace App\Services\Crawler;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

class HttpFetcher
{
    private Client $client;

    /**
     * @param Client|null $client
     */
    public function __construct(?Client $client = null)
    {
        $this->client = $client ?? new Client([
            'timeout' => 10,
            'allow_redirects' => true,
            'headers' => [
                'User-Agent' => 'SEO-Internal-Linking-Bot/1.0',
                'Accept' => 'text/html',
            ],
        ]);
    }

    /**
     * @param string $url
     *
     * @return string|null
     * @throws GuzzleException
     */
    public function fetch(string $url): ?string
    {
        try {
            $response = $this->client->get($url);

            if ($response->getStatusCode() !== 200) {
                return null;
            }

            return (string) $response->getBody();
        } catch (RequestException) {
            return null;
        }
    }
}
