# Payment Gateway Client

## Quick Start with Docker

```bash
# 1. Copy and fill in your environment variables
cp .env.example .env

# 2. Download certificates from https://badssl.com/download/
#    Place badssl.com-client.pem into the certificates/ directory

# 3. Build the Docker image
docker build -t payment-gateway .

# 4. Run all tests
docker run --rm payment-gateway ./vendor/bin/phpunit

# Run unit tests only
docker run --rm payment-gateway ./vendor/bin/phpunit --testsuite Unit

# Run integration tests only (requires valid .env and certificates)
docker run --rm payment-gateway ./vendor/bin/phpunit --testsuite Integration
```

## Requirements

- PHP 7.4+
- ext-curl
- Composer

## Installation

```bash
composer install
```

## Configuration

Copy `.env.example` to `.env` and fill in your values:

```bash
cp .env.example .env
```

```env
# API endpoint URL
API_URL=https://client.badssl.com

# Absolute path to client certificate (.pem)
CERTIFICATE_PATH=/absolute/path/to/client.pem

# Absolute path to private key (can be same file as CERTIFICATE_PATH)
CERTIFICATE_KEY_PATH=/absolute/path/to/client.pem

# Private key passphrase
KEY_PASSPHRASE=badssl.com

# HMAC secret key
HMAC_SECRET=your-secret-here
```

## Certificates

For testing purposes, download the client certificate from [badssl.com/download](https://badssl.com/download/):

- Download `badssl.com-client.pem`
- Place it in the `certificates/` directory
- Set `CERTIFICATE_PATH` and `CERTIFICATE_KEY_PATH` to the absolute path **inside the Docker container** — the project root is mounted at `/var/www/html`:

```env
CERTIFICATE_PATH=/var/www/html/certificates/badssl.com-client.pem
CERTIFICATE_KEY_PATH=/var/www/html/certificates/badssl.com-client.pem
```

- The passphrase is `badssl.com`

## Running Tests

```bash
# All tests
./vendor/bin/phpunit

# Unit tests only
./vendor/bin/phpunit --testsuite Unit

# Integration tests only (requires valid .env and certificates)
./vendor/bin/phpunit --testsuite Integration
```

## Project Structure

```
src/
  Client/
    PaymentGatewayClient.php   # Main client — sends mTLS request with HMAC signature
  Helper/
    Config.php                 # Reads configuration from $_ENV
  Service/
    HmacService.php            # Computes HMAC-SHA256 signature
  Exception/
    GatewayException.php       # Custom exception for gateway errors

tests/
  Unit/
    Client/
      PaymentGatewayClientTest.php  # Tests for error handling
    Service/
      HmacServiceTest.php           # Tests for HMAC computation
  Integration/
    Client/
      PaymentGatewayTest.php        # Real mTLS request to badssl.com
```

## How It Works

1. Payload is sorted by key and serialized to a query string
2. HMAC-SHA256 signature is computed using the payload and `HMAC_SECRET`
3. A GET request is sent with the payload as query parameters and the signature in the `X-Signature` header
4. mTLS is established by providing a client certificate to Guzzle
5. If the response is not in the 2xx range, a `GatewayException` is thrown