<?php

/**
 * Part of the AmazonGiftCode package.
 * Author: Kashyap Merai <kashyapk62@gmail.com>
 *
 */


namespace kamerk22\AmazonGiftCode\Config;


class Config implements ConfigInterface
{

    /**
     * The current Endpoint version.
     *
     * @var string
     */
    private $_endpoint;

    /**
     * The AWS Access Key.
     *
     * @var string
     */
    private $_accessKey;

    /**
     * The AWS Secret.
     *
     * @var string
     */
    private $_secretKey;

    /**
     * The Amazon Gift Card Partner.
     *
     * @var string
     */
    private $_partnerId;

    /**
     * The Amazon Gift Card Currency.
     *
     * @var string
     */
    private $_currency;


    public function __construct($key, $secret, $partner, $endpoint, $currency)
    {

        $this->setAccessKey($key ?: config('amazongiftcode.key'));
        $this->setSecret($secret ?: config('amazongiftcode.secret'));
        $this->setPartner($partner ?: config('amazongiftcode.partner'));
        $this->setEndpoint($endpoint ?: config('amazongiftcode.endpoint'));
        $this->setCurrency($currency ?: config('amazongiftcode.currency'));
    }

    /**
     * @return String
     */
    public function getEndpoint(): string
    {
        return $this->_endpoint;
    }


    /**
     * @param $endpoint
     * @return ConfigInterface
     */
    public function setEndpoint($endpoint): ConfigInterface
    {
        $this->_endpoint = parse_url($endpoint, PHP_URL_PATH);

        return $this;
    }

    /**
     * @return String
     */
    public function getAccessKey(): string
    {
        return $this->_accessKey;
    }

    /**
     * @param String $key
     * @return ConfigInterface
     */
    public function setAccessKey($key): ConfigInterface
    {
        $this->_accessKey = $key;

        return $this;
    }

    /**
     * @return String
     */
    public function getSecret(): string
    {
        return $this->_secretKey;
    }

    /**
     * @param String $secret
     * @return ConfigInterface
     */
    public function setSecret($secret): ConfigInterface
    {
        $this->_secretKey = $secret;

        return $this;
    }

    /**
     * @return String
     */
    public function getCurrency(): string
    {
        return $this->_currency;
    }

    /**
     * @param String $currency
     * @return ConfigInterface
     */
    public function setCurrency($currency): ConfigInterface
    {
        $this->_currency = $currency;

        return $this;
    }

    /**
     * @return String
     */
    public function getPartner(): string
    {
        return $this->_partnerId;
    }

    /**
     * @param String $partner
     * @return ConfigInterface
     */
    public function setPartner($partner): ConfigInterface
    {
        $this->_partnerId = $partner;

        return $this;
    }
}
