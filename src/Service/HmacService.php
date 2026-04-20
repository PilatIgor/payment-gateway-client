<?php

namespace SharpMinds\PaymentGatewayClient\Service;

use SharpMinds\PaymentGatewayClient\Helper\Config;

class HmacService
{
    private string $secret;

    public function __construct(Config $config)
    {
        $this->secret = $config->get('HMAC_SECRET');
    }

    public function generateHmac(array $payload): string
    {
        ksort($payload);
        $data = http_build_query($payload);
        return hash_hmac('sha256', $data, $this->secret);
    }
}