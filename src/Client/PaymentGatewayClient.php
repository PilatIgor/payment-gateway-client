<?php

namespace SharpMinds\PaymentGatewayClient\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use SharpMinds\PaymentGatewayClient\Exception\GatewayException;
use SharpMinds\PaymentGatewayClient\Service\HmacService;

class PaymentGatewayClient
{
    private HmacService $hmacService;
    private Client $httpClient;
    private string $certPath;
    private string $keyPath;
    private string $passphrase;

    public function __construct(
        HmacService $hmacService,
        Client $httpClient,
        string $certPath,
        string $keyPath,
        string $passphrase
    ) {
        $this->hmacService = $hmacService;
        $this->httpClient  = $httpClient;
        $this->certPath    = $certPath;
        $this->keyPath     = $keyPath;
        $this->passphrase  = $passphrase;
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
                    $this->certPath,
                    $this->passphrase
                ],
                'ssl_key' => [
                    $this->keyPath,
                    $this->passphrase
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
