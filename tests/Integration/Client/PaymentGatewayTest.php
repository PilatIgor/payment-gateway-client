<?php

namespace SharpMinds\PaymentGatewayClient\Tests\Integration\Client;

use PHPUnit\Framework\TestCase;
use SharpMinds\PaymentGatewayClient\Client\PaymentGatewayClient;
use Symfony\Component\Dotenv\Dotenv;

class PaymentGatewayTest extends TestCase
{
    private PaymentGatewayClient $client;
    private string $apiUrl;

    protected function setUp(): void
    {
        (new Dotenv())->loadEnv(dirname(__DIR__, 3) . '/.env');

        $this->apiUrl = $_ENV['API_URL'];
        $this->client = PaymentGatewayClient::create(
            certPath: $_ENV['CERTIFICATE_PATH'],
            keyPath: $_ENV['CERTIFICATE_KEY_PATH'],
            passphrase: $_ENV['KEY_PASSPHRASE'],
            hmacSecret: $_ENV['HMAC_SECRET'],
        );
    }

    public function testSendRealRequestWithMtls(): void
    {
        $payload = [
            'transaction_id' => '12345',
            'amount'         => '99.99',
            'currency'       => 'USD',
        ];

        $response = $this->client->send($this->apiUrl, $payload);

        $this->assertNotEmpty($response);
    }

}
