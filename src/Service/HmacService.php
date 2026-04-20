<?php

namespace SharpMinds\PaymentGatewayClient\Service;

class HmacService
{
    private string $secret;

    public function __construct(string $secret)
    {
        $this->secret = $secret;
    }

    public function generateHmac(array $payload): string
    {
        ksort($payload);
        $data = http_build_query($payload);
        return hash_hmac('sha256', $data, $this->secret);
    }
}