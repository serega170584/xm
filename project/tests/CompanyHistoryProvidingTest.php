<?php

namespace App\Tests;

use App\Service\CompanyHistoryApiProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class CompanyHistoryProvidingTest extends TestCase
{
    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testCompaniesHistorySuccessProviding(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())
                 ->method('getStatusCode')
                 ->will($this->returnValue(200));

        $response->expects($this->once())
                 ->method('getHeaders')
                 ->will($this->returnValue(['content-type' => ['application/json']]));

        $prices = ['prices' => [
            'date' => '2023-03-04',
            'open' => 1.20,
            'high' => 2.40,
            'low' => 3.40,
            'close' => 4.40,
            'volume' => 500,
            'adjclose' => 1.60
        ]];

        $response->expects($this->once())
                 ->method('toArray')
                 ->will($this->returnValue($prices));

        $client = $this->createMock(HttpClientInterface::class);

        $client->expects($this->once())
               ->method('request')
               ->will($this->returnValue($response));

        $provider = new CompanyHistoryApiProvider($client, '11111');
        $this->assertTrue($provider->getPrices('') === [
                              'date' => '2023-03-04',
                              'open' => 1.20,
                              'high' => 2.40,
                              'low' => 3.40,
                              'close' => 4.40,
                              'volume' => 500,
                              'adjclose' => 1.60
                          ]);
    }

    /**
     * @dataProvider headersProvider
     */
    public function testCompaniesUnSuccessProviding($status, $contentType): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->any())
                 ->method('getStatusCode')
                 ->will($this->returnValue($status));

        $response->expects($this->any())
                 ->method('getHeaders')
                 ->will($this->returnValue(['content-type' => [$contentType]]));

        $this->expectException(\Exception::class);

        $client = $this->createMock(HttpClientInterface::class);

        $provider = new CompanyHistoryApiProvider($client, '11111');
        $provider->getPrices('');
    }

    public function headersProvider(): \Generator
    {
        yield [200, 'application/json'];
        yield [500, 'application/json'];
        yield [500, 'text/plain'];
    }
}
