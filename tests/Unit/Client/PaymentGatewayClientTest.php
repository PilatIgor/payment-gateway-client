<?php

namespace SharpMinds\PaymentGatewayClient\Tests\Unit\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use SharpMinds\PaymentGatewayClient\Client\PaymentGatewayClient;
use SharpMinds\PaymentGatewayClient\Exception\GatewayException;
use SharpMinds\PaymentGatewayClient\Service\HmacService;

class PaymentGatewayClientTest extends TestCase
{
    private HmacService $hmacService;

    protected function setUp(): void
    {
        $this->hmacService = $this->createMock(HmacService::class);
        $this->hmacService->method('generateHmac')->willReturn('fake-signature');
    }

    private function makeClient(Client $httpClient): PaymentGatewayClient
    {
        return new PaymentGatewayClient(
            $this->hmacService,
            $httpClient,
            '/path/to/cert.pem',
            '/path/to/key.pem',
            'passphrase'
        );
    }

    public function testThrowsExceptionOnNonSuccessResponse(): void
    {
        $httpClient = $this->createMock(Client::class);
        $httpClient->method('get')->willReturn(new Response(404));

        $this->expectException(GatewayException::class);
        $this->expectExceptionMessageMatches('/Unexpected HTTP response code: 404/');

        $this->makeClient($httpClient)->send('https://example.com', ['amount' => '99.99']);
    }

    public function testThrowsExceptionOnConnectionFailure(): void
    {
        $httpClient = $this->createMock(Client::class);
        $httpClient->method('get')->willThrowException(
            new RequestException('Connection error', new Request('GET', 'https://example.com'))
        );

        $this->expectException(GatewayException::class);
        $this->expectExceptionMessageMatches('/Request connection failed/');

        $this->makeClient($httpClient)->send('https://example.com', ['amount' => '99.99']);
    }
}
