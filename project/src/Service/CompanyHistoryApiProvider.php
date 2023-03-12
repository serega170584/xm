<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CompanyHistoryApiProvider
{
    private HttpClientInterface $client;

    private string $token;

    public function __construct(HttpClientInterface $client, string $token)
    {
        $this->client = $client;
        $this->token = $token;
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws \Exception
     */
    public function getPrices(string $url): array
    {
        $response = $this->client->request('GET', $url, [
            'headers' => [
                'X-RapidAPI-Key' => $this->token,
                'X-RapidAPI-Host' => 'yh-finance.p.rapidapi.com'
            ]
        ]);

        $code = $response->getStatusCode();
        $headers = $response->getHeaders();
        $contentType = $headers['content-type'][0] ?? NULL;

        if (200 !== $code) {
            throw new \Exception('Wrong code status');
        }

        if ('application/json' !== $contentType) {
            throw new \Exception('Wrong content type');
        }

        $prices = $response->toArray();
        $prices = $prices['prices'] ?? [];

        return $prices;
    }
}