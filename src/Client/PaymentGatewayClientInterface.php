<?php

namespace SharpMinds\PaymentGatewayClient\Client;

interface PaymentGatewayClientInterface
{
    public function send(string $url, array $payload): string;
}
