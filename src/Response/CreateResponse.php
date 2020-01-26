<?php

/**
 * Part of the AmazonGiftCode package.
 * Author: Kashyap Merai <kashyapk62@gmail.com>
 *
 */


namespace kamerk22\AmazonGiftCode\Response;


class CreateResponse
{

    /**
     * Amazon Gift Card gcId.
     *
     * @var string
     */
    protected $_id;

    /**
     * Amazon Gift Card creationRequestId
     *
     * @var string
     */
    protected $_creation_request_id;

    /**
     * Amazon Gift Card gcClaimCode
     *
     * @var string
     */
    protected $_claim_code;

    /**
     * Amazon Gift Card amount
     *
     * @var string
     */
    protected $_value;

    /**
     * Amazon Gift Card currency
     *
     * @var string
     */
    protected $_currency;
    /**
     * Amazon Gift Card status
     *
     * @var string
     */
    protected $_status;
    /**
     * Amazon Gift Card Expiration Date
     *
     * @var string
     */
    protected $_expiration_date;

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
    public function getId(): string
    {
        return $this->_id;
    }

    /**
     * @return string
     */
    public function getCreationRequestId(): string
    {
        return $this->_creation_request_id;
    }


    /**
     * @return string
     */
    public function getClaimCode(): string
    {
        return $this->_claim_code;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->_value;
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
    public function getExpirationDate(): string
    {
        return $this->_expiration_date;
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
     * @return CreateResponse
     */
    public function parseJsonResponse($jsonResponse): self
    {
        if (!is_array($jsonResponse)) {
            throw new \RuntimeException('Response must be a scalar value');
        }
        if (array_key_exists('gcId', $jsonResponse)) {
            $this->_id = $jsonResponse['gcId'];
        }
        if (array_key_exists('creationRequestId', $jsonResponse)) {
            $this->_creation_request_id = $jsonResponse['creationRequestId'];
        }
        if (array_key_exists('gcClaimCode', $jsonResponse)) {
            $this->_claim_code = $jsonResponse['gcClaimCode'];
        }
        if (array_key_exists('amount', $jsonResponse['cardInfo']['value'])) {
            $this->_value = $jsonResponse['cardInfo']['value']['amount'];
        }
        if (array_key_exists('currencyCode', $jsonResponse['cardInfo']['value'])) {
            $this->_currency = $jsonResponse['cardInfo']['value']['currencyCode'];
        }
        if (array_key_exists('gcExpirationDate', $jsonResponse)) {
            $this->_expiration_date = $jsonResponse['gcExpirationDate'];
        }

        return $this;

    }

}