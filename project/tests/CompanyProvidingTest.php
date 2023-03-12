<?php

namespace App\Tests;

use App\Service\CompanyApiProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class CompanyProvidingTest extends TestCase
{
    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testCompaniesSuccessProviding(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())
            ->method('getStatusCode')
            ->will($this->returnValue(200));

        $response->expects($this->once())
            ->method('getHeaders')
            ->will($this->returnValue(['content-type' => ['text/plain']]));

        $companies = [[
            'Company Name' => '123',
            'Financial Status' => '123',
            'Market Category' => '123',
            'Round Lot Size' => '123',
            'Security Name' => '123',
            'Symbol' => '123',
            'Test Issue' => '123'
        ]];

        $response->expects($this->once())
            ->method('toArray')
            ->will($this->returnValue($companies));

        $client = $this->createMock(HttpClientInterface::class);

        $client->expects($this->once())
            ->method('request')
            ->will($this->returnValue($response));

        $provider = new CompanyApiProvider($client);
        $this->assertTrue($provider->getCompanies('') === $companies);
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

        $provider = new CompanyApiProvider($client);
        $provider->getCompanies('');
    }

    public function headersProvider(): \Generator
    {
        yield [200, 'application/json'];
        yield [500, 'application/json'];
        yield [500, 'text/plain'];
    }
}
