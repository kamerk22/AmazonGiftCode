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
    private $endpoint;

    /**
     * The AWS Access Key.
     *
     * @var string
     */
    private $accessKey;

    /**
     * The AWS Secret.
     *
     * @var string
     */
    private $secretKey;

    /**
     * The Amazon Gift Card Partner.
     *
     * @var string
     */
    private $partnerId;

    /**
     * The Amazon Gift Card Currency.
     *
     * @var string
     */
    private $currency;


    public function __construct($key, $secret, $partner, $endpoint, $currency)
    {

        $this->setAccessKey($key ?: config('amazongiftcode.key'));
        $this->setSecret($secret ?: config('amazongiftcode.secret'));
        $this->setPartner($partner ?: config('amazongiftcode.partner'));
        $this->setEndpoint($endpoint ?: config('amazongiftcode.endpoint'));
        $this->setCurrency($currency ?: config('amazongiftcode.currency'));

        if (!$this->accessKey) {
            throw new \RuntimeException('The AWS Access Key is not defined!');
        }
        if (!$this->endpoint) {
            throw new \RuntimeException('The AWS Endpoint is not defined!');
        }
        if (!$this->secretKey) {
            throw new \RuntimeException('The AWS Secret is not defined!');
        }
        if (!$this->partnerId) {
            throw new \RuntimeException('The AWS Partner is not defined!');
        }
        if (!$this->currency) {
            throw new \RuntimeException('The Currency is not defined!');
        }
    }

    /**
     * @return String
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * @param $endpoint
     * @return $this
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = parse_url($endpoint, PHP_URL_HOST);

        return $this;
    }

    /**
     * @return String
     */
    public function getAccessKey()
    {
        return $this->accessKey;
    }

    /**
     * @param String $key
     * @return $this
     */
    public function setAccessKey($key)
    {
        $this->accessKey = $key;

        return $this;
    }

    /**
     * @return String
     */
    public function getSecret()
    {
        return $this->secretKey;
    }

    /**
     * @param String $secret
     * @return $this
     */
    public function setSecret($secret)
    {
        $this->secretKey = $secret;

        return $this;
    }

    /**
     * @return String
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param String $currency
     * @return $this
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return String
     */
    public function getPartner()
    {
        return $this->partnerId;
    }

    /**
     * @param String $partner
     * @return $this
     */
    public function setPartner($partner)
    {
        $this->partnerId = $partner;

        return $this;
    }
}