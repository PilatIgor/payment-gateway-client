<?php

namespace Haverllok\PaymentGatewayClient\Client;

interface PaymentGatewayClientInterface
{
    public function send(string $url, array $payload): string;
}
