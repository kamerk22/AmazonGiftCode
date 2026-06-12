<?php

namespace kamerk22\AmazonGiftCode;

use kamerk22\AmazonGiftCode\AWS\AWS;
use kamerk22\AmazonGiftCode\Client\ClientInterface;
use kamerk22\AmazonGiftCode\Config\Config;
use kamerk22\AmazonGiftCode\Exceptions\AmazonErrors;

class AmazonGiftCode
{

    private $_config;

    /**
     * @var ClientInterface|null
     */
    private $_client;

    /**
     * AmazonGiftCode constructor.
     *
     * @param null $key
     * @param null $secret
     * @param null $partner
     * @param null $endpoint
     * @param null $currency
     * @param ClientInterface|null $client Optional HTTP client. Defaults to the
     *                                     cURL-based Client; inject a fake for testing.
     */
    public function __construct($key = null, $secret = null, $partner = null, $endpoint = null, $currency = null, ?ClientInterface $client = null)
    {
        $this->_config = new Config($key, $secret, $partner, $endpoint, $currency);
        $this->_client = $client;
    }

    /**
     * @param Float $value
     * @param string $creationRequestId
     * @return Response\CreateResponse
     *
     * @throws AmazonErrors
     */
    public function buyGiftCard(Float $value, ?string $creationRequestId = null): Response\CreateResponse
    {
        return (new AWS($this->_config, $this->_client))->getCode($value, $creationRequestId);
    }


    /**
     * @param string $creationRequestId
     * @param string $gcId
     * @return Response\CancelResponse
     */
    public function cancelGiftCard(string $creationRequestId, string $gcId): Response\CancelResponse
    {
        return (new AWS($this->_config, $this->_client))->cancelCode($creationRequestId, $gcId);
    }

    /**
     * @return Response\CreateBalanceResponse
     *
     * @throws AmazonErrors
     */
    public function getAvailableFunds(): Response\CreateBalanceResponse
    {
        return (new AWS($this->_config, $this->_client))->getBalance();
    }

    /**
     * AmazonGiftCode make own client.
     *
     * @param null $key
     * @param null $secret
     * @param null $partner
     * @param null $endpoint
     * @param null $currency
     * @return AmazonGiftCode
     */
    public static function make($key = null, $secret = null, $partner = null, $endpoint = null, $currency = null): AmazonGiftCode
    {
        return new static($key, $secret, $partner, $endpoint, $currency);
    }

}
