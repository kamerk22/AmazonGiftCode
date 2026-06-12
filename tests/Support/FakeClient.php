<?php

namespace kamerk22\AmazonGiftCode\Tests\Support;

use kamerk22\AmazonGiftCode\Client\ClientInterface;

/**
 * Test double for the cURL client. Returns a canned JSON body instead of
 * hitting the Amazon Incentives API, and records the last request it received.
 */
class FakeClient implements ClientInterface
{
    /** @var string */
    private $response;

    /** @var string|null */
    public $lastUrl;

    /** @var array|null */
    public $lastHeaders;

    /** @var string|null */
    public $lastParams;

    public function __construct(string $response)
    {
        $this->response = $response;
    }

    public function request($url, $headers, $params): string
    {
        $this->lastUrl = $url;
        $this->lastHeaders = $headers;
        $this->lastParams = $params;

        return $this->response;
    }
}
