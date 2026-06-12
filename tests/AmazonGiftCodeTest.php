<?php

namespace kamerk22\AmazonGiftCode\Tests;

use kamerk22\AmazonGiftCode\AmazonGiftCode;
use kamerk22\AmazonGiftCode\Response\CancelResponse;
use kamerk22\AmazonGiftCode\Response\CreateBalanceResponse;
use kamerk22\AmazonGiftCode\Response\CreateResponse;
use kamerk22\AmazonGiftCode\Tests\Support\FakeClient;

/**
 * End-to-end coverage of the public API with the HTTP layer faked, so the full
 * request-build -> client -> response-parse path is exercised without network.
 */
class AmazonGiftCodeTest extends TestCase
{
    private function giftCode(FakeClient $client): AmazonGiftCode
    {
        return new AmazonGiftCode(null, null, null, null, null, $client);
    }

    public function test_buy_gift_card_returns_parsed_create_response(): void
    {
        $client = new FakeClient(json_encode([
            'cardInfo' => [
                'cardStatus' => 'Fulfilled',
                'value' => ['amount' => 25, 'currencyCode' => 'USD'],
            ],
            'creationRequestId' => 'Examp_abc',
            'gcClaimCode' => 'WXYZ-123456-ABCD',
            'gcExpirationDate' => '2030-01-01T00:00:00Z',
            'gcId' => 'A2ZACI3RGNN4FB',
            'status' => 'SUCCESS',
        ]));

        $response = $this->giftCode($client)->buyGiftCard(25.0, 'Examp_abc');

        $this->assertInstanceOf(CreateResponse::class, $response);
        $this->assertSame('WXYZ-123456-ABCD', $response->getClaimCode());
        $this->assertSame('25', $response->getValue());

        // The request was signed and aimed at the configured endpoint/operation.
        $this->assertSame('https://agcod-v2-gamma.amazon.com/CreateGiftCard', $client->lastUrl);
        $this->assertSame('Examp_abc', json_decode($client->lastParams, true)['creationRequestId']);
    }

    public function test_cancel_gift_card_returns_parsed_cancel_response(): void
    {
        $client = new FakeClient(json_encode([
            'creationRequestId' => 'Examp_abc',
            'gcId' => 'A2ZACI3RGNN4FB',
            'status' => 'SUCCESS',
        ]));

        $response = $this->giftCode($client)->cancelGiftCard('Examp_abc', 'A2ZACI3RGNN4FB');

        $this->assertInstanceOf(CancelResponse::class, $response);
        $this->assertSame('A2ZACI3RGNN4FB', $response->getId());
        $this->assertSame('https://agcod-v2-gamma.amazon.com/CancelGiftCard', $client->lastUrl);
    }

    public function test_get_available_funds_returns_parsed_balance_response(): void
    {
        $client = new FakeClient(json_encode([
            'availableFunds' => ['amount' => 5000, 'currencyCode' => 'USD'],
            'status' => 'SUCCESS',
            'timestamp' => '20240101T000000Z',
        ]));

        $response = $this->giftCode($client)->getAvailableFunds();

        $this->assertInstanceOf(CreateBalanceResponse::class, $response);
        $this->assertSame('5000', $response->getAmount());
        $this->assertSame('https://agcod-v2-gamma.amazon.com/GetAvailableFunds', $client->lastUrl);
    }
}
