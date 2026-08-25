# Payment Gateway Client

A PHP client for communicating with a payment gateway over a secured mTLS channel with HMAC-SHA256 request signing.

## What the library does

`PaymentGatewayClient` builds a GET request to the payment gateway:

1. The payload (a parameter array) is sorted by keys and serialized into a query string.
2. An HMAC-SHA256 signature is computed for it based on a secret key (`HmacService`).
3. The request is sent with the payload in the query parameters and the signature in the `X-Signature` header.
4. The connection is established over mTLS — the client certificate and private key are passed to Guzzle.
5. If the gateway's response falls outside the 2xx range, a `GatewayException` is thrown.

## Usage

```php
use Haverllok\PaymentGatewayClient\Client\PaymentGatewayClient;

$client = PaymentGatewayClient::create(
    hmacSecret: 'your-secret',
    certPath: '/path/to/client.pem',
    keyPath: '/path/to/client.pem',
    passphrase: 'cert-passphrase'
);

$response = $client->send('https://gateway.example.com/api', [
    'amount'   => 100,
    'currency' => 'UAH',
]);
```

## Structure

```
src/
  Client/
    PaymentGatewayClient.php   # Main client: mTLS request with HMAC signature
    PaymentGatewayClientInterface.php
  Service/
    HmacService.php            # HMAC-SHA256 signature computation
  Exception/
    GatewayException.php       # Exception for gateway errors (failed connection, response code outside 2xx)
```

## Configuration

The client requires: the gateway URL, the path to the client certificate (mTLS), the path to the private key, the key passphrase, and the secret for the HMAC signature.