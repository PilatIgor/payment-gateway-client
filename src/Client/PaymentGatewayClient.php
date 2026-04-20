<?php

namespace SharpMinds\PaymentGatewayClient\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use SharpMinds\PaymentGatewayClient\Exception\GatewayException;
use SharpMinds\PaymentGatewayClient\Helper\Config;
use SharpMinds\PaymentGatewayClient\Service\HmacService;

class PaymentGatewayClient
{
    private Config $config;
    private HmacService $hmacService;
    private Client $httpClient;

    public function __construct(Config $config, HmacService $hmacService, Client $httpClient)
    {
        $this->config = $config;
        $this->hmacService = $hmacService;
        $this->httpClient = $httpClient;
    }

    public function send(string $url, array $payload): string
    {
        $hmacSignature = $this->hmacService->generateHmac($payload);

        try {
            $response = $this->httpClient->get($url, [
                'headers' => [
                    'X-Signature' => $hmacSignature,
                ],
                'verify'  => true,
                'query'   => $payload,
                'cert'    => [
                    $this->config->get('CERTIFICATE_PATH'),
                    $this->config->get('KEY_PASSPHRASE')
                ],
                'ssl_key' => [
                    $this->config->get('CERTIFICATE_KEY_PATH'),
                    $this->config->get('KEY_PASSPHRASE')
                ],
            ]);
        } catch (GuzzleException $e) {
            throw GatewayException::connectionFailed($e->getMessage());
        }

        $statusCode = $response->getStatusCode();

        if ($statusCode < 200 || $statusCode >= 300) {
            throw GatewayException::invalidResponseCode($statusCode);
        }

        return $response->getBody();
    }
}
