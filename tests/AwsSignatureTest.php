<?php

namespace kamerk22\AmazonGiftCode\Tests;

use kamerk22\AmazonGiftCode\AWS\AWS;
use kamerk22\AmazonGiftCode\Config\Config;
use PHPUnit\Framework\Attributes\DataProvider;

class AwsSignatureTest extends TestCase
{
    private function aws(string $endpoint = 'https://agcod-v2-gamma.amazon.com'): AWS
    {
        return new AWS(new Config('AKIA', 'secret', 'Examp', $endpoint, 'USD'));
    }

    #[DataProvider('regionProvider')]
    public function test_region_is_derived_from_endpoint(string $endpoint, string $expectedRegion): void
    {
        $this->assertSame($expectedRegion, $this->aws($endpoint)->getRegion());
    }

    public static function regionProvider(): array
    {
        return [
            'na production'  => ['https://agcod-v2.amazon.com', 'us-east-1'],
            'na sandbox'     => ['https://agcod-v2-gamma.amazon.com', 'us-east-1'],
            'eu production'  => ['https://agcod-v2-eu.amazon.com', 'eu-west-1'],
            'eu sandbox'     => ['https://agcod-v2-eu-gamma.amazon.com', 'eu-west-1'],
            'fe production'  => ['https://agcod-v2-fe.amazon.com', 'us-west-2'],
            'fe sandbox'     => ['https://agcod-v2-fe-gamma.amazon.com', 'us-west-2'],
        ];
    }

    public function test_gift_card_payload_has_expected_shape(): void
    {
        $payload = json_decode($this->aws()->getGiftCardPayload('10.00', 'req-1'), true);

        $this->assertSame('req-1', $payload['creationRequestId']);
        $this->assertSame('Examp', $payload['partnerId']);
        $this->assertSame('USD', $payload['value']['currencyCode']);
        $this->assertEquals(10, $payload['value']['amount']);
    }

    public function test_gift_card_payload_generates_creation_id_when_missing(): void
    {
        $payload = json_decode($this->aws()->getGiftCardPayload('5'), true);

        $this->assertStringStartsWith('Examp_', $payload['creationRequestId']);
    }

    public function test_cancel_payload_has_expected_shape(): void
    {
        $payload = json_decode($this->aws()->getCancelGiftCardPayload('req-1', 'gc-1'), true);

        $this->assertSame('req-1', $payload['creationRequestId']);
        $this->assertSame('Examp', $payload['partnerId']);
        $this->assertSame('gc-1', $payload['gcId']);
    }

    public function test_available_funds_payload_has_expected_shape(): void
    {
        $payload = json_decode($this->aws()->getAvailableFundsPayload(), true);

        $this->assertSame(['partnerId' => 'Examp'], $payload);
    }

    public function test_build_hash_is_sha256_hex(): void
    {
        $this->assertSame(hash('sha256', 'payload'), $this->aws()->buildHash('payload'));
    }

    public function test_hmac_matches_php_reference(): void
    {
        $aws = $this->aws();

        $this->assertSame(hash_hmac('sha256', 'data', 'key', false), $aws->hmac('data', 'key', false));
        $this->assertSame(hash_hmac('sha256', 'data', 'key', true), $aws->hmac('data', 'key', true));
    }

    public function test_canonical_request_targets_the_operation(): void
    {
        $canonical = $this->aws()->getCanonicalRequest('CreateGiftCard', '{}');

        $this->assertStringStartsWith("POST\n/CreateGiftCard\n", $canonical);
        $this->assertStringContainsString('host:agcod-v2-gamma.amazon.com', $canonical);
        $this->assertStringEndsWith(hash('sha256', '{}'), $canonical);
    }

    public function test_string_to_sign_uses_sigv4_scope(): void
    {
        $stringToSign = $this->aws()->buildStringToSign('abc123');

        $this->assertStringStartsWith("AWS4-HMAC-SHA256\n", $stringToSign);
        $this->assertStringContainsString('us-east-1/AGCODService/aws4_request', $stringToSign);
        $this->assertStringEndsWith("\nabc123", $stringToSign);
    }
}
