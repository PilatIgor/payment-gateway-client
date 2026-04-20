<?php

namespace SharpMinds\PaymentGatewayClient\Helper;


class Config
{
    public function get(string $key, ?string $default = null): ?string
    {
        $value = $_ENV[$key] ?? getenv($key);
        return $value !== false ? $value : $default;
    }
}