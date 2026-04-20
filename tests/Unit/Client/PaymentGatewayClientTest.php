<?php

namespace SharpMinds\PaymentGatewayClient\Tests\Unit\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use SharpMinds\PaymentGatewayClient\Client\PaymentGatewayClient;
use SharpMinds\PaymentGatewayClient\Exception\GatewayException;
use SharpMinds\PaymentGatewayClient\Helper\Config;
use SharpMinds\PaymentGatewayClient\Service\HmacService;

class PaymentGatewayClientTest extends TestCase
{
    private Config $config;
    private HmacService $hmacService;

    protected function setUp(): void
    {
        $this->config = $this->createMock(Config::class);
        $this->config->method('get')->willReturnMap([
            ['CERTIFICATE_PATH', null, '/path/to/cert.pem'],
            ['CERTIFICATE_KEY_PATH', null, '/path/to/cert.pem'],
            ['KEY_PASSPHRASE', null, 'passphrase'],
        ]);

        $this->hmacService = $this->createMock(HmacService::class);
        $this->hmacService->method('generateHmac')->willReturn('fake-signature');
    }

    public function testThrowsExceptionOnNonSuccessResponse(): void
    {
        $httpClient = $this->createMock(Client::class);
        $httpClient->method('get')->willReturn(new Response(404));

        $client = new PaymentGatewayClient($this->config, $this->hmacService, $httpClient);

        $this->expectException(GatewayException::class);
        $this->expectExceptionMessageMatches('/Unexpected HTTP response code: 404/');

        $client->send('https://example.com', ['amount' => '99.99']);
    }

    public function testThrowsExceptionOnConnectionFailure(): void
    {
        $httpClient = $this->createMock(Client::class);
        $httpClient->method('get')->willThrowException(
            new RequestException('Connection error', new Request('GET', 'https://example.com'))
        );

        $client = new PaymentGatewayClient($this->config, $this->hmacService, $httpClient);

        $this->expectException(GatewayException::class);
        $this->expectExceptionMessageMatches('/Request connection failed/');

        $client->send('https://example.com', ['amount' => '99.99']);
    }
}
