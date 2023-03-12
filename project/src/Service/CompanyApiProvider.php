<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CompanyApiProvider
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function getCompanies(string $url): array
    {
        $response = $this->client->request('GET', $url);
        $code = $response->getStatusCode();
        $headers = $response->getHeaders();
        $contentType = $headers['content-type'][0] ?? NULL;

        if (200 !== $code) {
            throw new \Exception('Wrong code status');
        }

        if ('text/plain' !== $contentType) {
            throw new \Exception('Wrong content type');
        }

        return $response->toArray();
    }
}