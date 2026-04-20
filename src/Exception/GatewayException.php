<?php

namespace SharpMinds\PaymentGatewayClient\Exception;

use RuntimeException;

class GatewayException extends RuntimeException
{
    public static function invalidResponseCode(int $code): self
    {
        return new self("Unexpected HTTP response code: {$code}");
    }

    public static function connectionFailed(string $response): self
    {
        return new self("Request connection failed: {$response}");
    }
}
