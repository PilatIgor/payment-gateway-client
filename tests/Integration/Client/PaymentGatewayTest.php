<?php

namespace SharpMinds\PaymentGatewayClient\Tests\Integration\Client;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use SharpMinds\PaymentGatewayClient\Client\PaymentGatewayClient;
use SharpMinds\PaymentGatewayClient\Service\HmacService;
use Symfony\Component\Dotenv\Dotenv;

class PaymentGatewayTest extends TestCase
{
    private PaymentGatewayClient $client;
    private string $apiUrl;

    protected function setUp(): void
    {
        (new Dotenv())->loadEnv(dirname(__DIR__, 3) . '/.env');

        $this->apiUrl = $_ENV['API_URL'];
        $this->client = new PaymentGatewayClient(
            new HmacService($_ENV['HMAC_SECRET']),
            new Client(),
            $_ENV['CERTIFICATE_PATH'],
            $_ENV['CERTIFICATE_KEY_PATH'],
            $_ENV['KEY_PASSPHRASE']
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
