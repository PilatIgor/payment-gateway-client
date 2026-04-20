<?php

namespace SharpMinds\PaymentGatewayClient\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use SharpMinds\PaymentGatewayClient\Helper\Config;
use SharpMinds\PaymentGatewayClient\Service\HmacService;

class HmacServiceTest extends TestCase
{
    private HmacService $hmacService;

    protected function setUp(): void
    {
        $config = $this->createMock(Config::class);
        $config->method('get')->with('HMAC_SECRET')->willReturn('test-secret');

        $this->hmacService = new HmacService($config);
    }

    public function testGenerateHmacReturnsSha256Hash(): void
    {
        $payload = ['transaction_id' => '12345', 'amount' => '99.99', 'currency' => 'USD'];

        $result = $this->hmacService->generateHmac($payload);

        $expected = hash_hmac('sha256', 'amount=99.99&currency=USD&transaction_id=12345', 'test-secret');
        $this->assertSame($expected, $result);
    }

    public function testGenerateHmacIsDeterministic(): void
    {
        $payload = ['transaction_id' => '12345', 'amount' => '99.99', 'currency' => 'USD'];

        $first = $this->hmacService->generateHmac($payload);
        $second = $this->hmacService->generateHmac($payload);

        $this->assertSame($first, $second);
    }

    public function testGenerateHmacIsSameRegardlessOfKeyOrder(): void
    {
        $payload1 = ['transaction_id' => '12345', 'amount' => '99.99', 'currency' => 'USD'];
        $payload2 = ['currency' => 'USD', 'amount' => '99.99', 'transaction_id' => '12345'];

        $this->assertSame(
            $this->hmacService->generateHmac($payload1),
            $this->hmacService->generateHmac($payload2)
        );
    }
}
