<?php

namespace SharpMinds\PaymentGatewayClient\Tests\Integration\Client;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use SharpMinds\PaymentGatewayClient\Client\PaymentGatewayClient;
use SharpMinds\PaymentGatewayClient\Exception\GatewayException;
use SharpMinds\PaymentGatewayClient\Helper\Config;
use SharpMinds\PaymentGatewayClient\Service\HmacService;
use Symfony\Component\Dotenv\Dotenv;

class PaymentGatewayTest extends TestCase
{
    private PaymentGatewayClient $client;
    private Config $config;

    protected function setUp(): void
    {
        (new Dotenv())->loadEnv(dirname(__DIR__, 3) . '/.env');

        $this->config = new Config();
        $this->client = new PaymentGatewayClient(
            $this->config,
            new HmacService($this->config),
            new Client()
        );
    }

    public function testSendRealRequestWithMtls(): void
    {
        $payload = [
            'transaction_id' => '12345',
            'amount'         => '99.99',
            'currency'       => 'USD',
        ];

        $response = $this->client->send($this->config->get('API_URL'), $payload);

        $this->assertNotEmpty($response);
    }

}
