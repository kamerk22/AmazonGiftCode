<?php

/**
 * Part of the AmazonGiftCode package.
 * Author: Kashyap Merai <kashyapk62@gmail.com>
 *
 */


namespace kamerk22\AmazonGiftCode\Response;


use Illuminate\Support\Facades\Log;

class CreateBalanceResponse
{
    /**
     * Amazon Gift Card Balance Amount
     *
     * @var string
     */
    protected $_amount;
    /**
     * Amazon Gift Card Balance Currency
     *
     * @var string
     */
    protected $_currency;
    /**
     * Amazon Gift Card Balance Status
     *
     * @var string
     */
    protected $_status;
    /**
     * Amazon Gift Card Balance Timestamp
     *
     * @var string
     */
    protected $_timestamp;
    /**
     * Amazon Gift Card Raw JSON
     *
     * @var string
     */
    protected $_raw_json;

    /**
     * Response constructor.
     * @param $jsonResponse
     */
    public function __construct($jsonResponse)
    {
        $this->_raw_json = $jsonResponse;
        $this->_status = TRUE;
        $this->parseJsonResponse($jsonResponse);
    }

    /**
     * @return string
     */
    public function getAmount(): string
    {
        return $this->_amount;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->_currency;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->_status;
    }

    /**
     * @return string
     */
    public function getTimestamp(): string
    {
        return $this->_timestamp;
    }

    /**
     * @return string
     */
    public function getRawJson(): string
    {
        return json_encode($this->_raw_json);
    }

    /**
     * @param $jsonResponse
     * @return CreateBalanceResponse
     */
    public function parseJsonResponse($jsonResponse): self
    {
        if (!is_array($jsonResponse)) {
            throw new \RuntimeException('Response must be a scalar value');
        }
        if (array_key_exists('amount', $jsonResponse['availableFunds'])) {
            $this->_amount = $jsonResponse['availableFunds']['amount'];
        }
        if (array_key_exists('currencyCode', $jsonResponse['availableFunds'])) {
            $this->_currency = $jsonResponse['availableFunds']['currencyCode'];
        }
        if (array_key_exists('status', $jsonResponse)) {
            $this->_status = $jsonResponse['status'];
        }
        if (array_key_exists('timestamp', $jsonResponse)) {
            $this->_timestamp = $jsonResponse['timestamp'];
        }

        return $this;

    }

}
