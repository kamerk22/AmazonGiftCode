<?php

namespace kamerk22\AmazonGiftCode\Tests;

use kamerk22\AmazonGiftCode\Response\CancelResponse;
use kamerk22\AmazonGiftCode\Response\CreateBalanceResponse;
use kamerk22\AmazonGiftCode\Response\CreateResponse;

class ResponseTest extends TestCase
{
    private function createPayload(): array
    {
        return [
            'cardInfo' => [
                'cardNumber' => null,
                'cardStatus' => 'Fulfilled',
                'value' => ['amount' => 10, 'currencyCode' => 'USD'],
            ],
            'creationRequestId' => 'Examp_123',
            'gcClaimCode' => 'ABCD-EFGHIJ-KLMN',
            'gcExpirationDate' => '2030-01-01T00:00:00Z',
            'gcId' => 'A2ZACI3RGNN4FB',
            'status' => 'SUCCESS',
        ];
    }

    public function test_create_response_exposes_card_details(): void
    {
        $response = new CreateResponse($this->createPayload());

        $this->assertSame('A2ZACI3RGNN4FB', $response->getId());
        $this->assertSame('Examp_123', $response->getCreationRequestId());
        $this->assertSame('ABCD-EFGHIJ-KLMN', $response->getClaimCode());
        $this->assertSame('10', $response->getValue());
        $this->assertSame('USD', $response->getCurrency());
        $this->assertSame('2030-01-01T00:00:00Z', $response->getExpirationDate());
        $this->assertSame('Fulfilled', $response->getCardStatus());
        $this->assertJson($response->getRawJson());
    }

    public function test_create_response_rejects_non_array_payload(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Response must be a scalar value');

        new CreateResponse('not-an-array');
    }

    public function test_create_response_tolerates_missing_card_info(): void
    {
        // Amazon may return an error-shaped body without cardInfo; parsing it
        // must not raise an undefined-key error.
        $response = new CreateResponse([
            'creationRequestId' => 'Examp_123',
            'status' => 'FAILURE',
        ]);

        $this->assertSame('Examp_123', $response->getCreationRequestId());
    }

    public function test_balance_response_tolerates_missing_available_funds(): void
    {
        $response = new CreateBalanceResponse([
            'status' => 'FAILURE',
        ]);

        $this->assertSame('FAILURE', $response->getStatus());
    }

    public function test_cancel_response_exposes_ids(): void
    {
        $response = new CancelResponse([
            'creationRequestId' => 'Examp_123',
            'gcId' => 'A2ZACI3RGNN4FB',
            'status' => 'SUCCESS',
        ]);

        $this->assertSame('A2ZACI3RGNN4FB', $response->getId());
        $this->assertSame('Examp_123', $response->getCreationRequestId());
        $this->assertJson($response->getRawJson());
    }

    public function test_balance_response_exposes_available_funds(): void
    {
        $response = new CreateBalanceResponse([
            'availableFunds' => ['amount' => 1000, 'currencyCode' => 'USD'],
            'status' => 'SUCCESS',
            'timestamp' => '20240101T000000Z',
        ]);

        $this->assertSame('1000', $response->getAmount());
        $this->assertSame('USD', $response->getCurrency());
        $this->assertSame('SUCCESS', $response->getStatus());
        $this->assertSame('20240101T000000Z', $response->getTimestamp());
    }
}
