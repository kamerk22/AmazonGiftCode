<?php

/**
 * Part of the AmazonGiftCode package.
 * Author: Kashyap Merai <kashyapk62@gmail.com>
 *
 */


namespace kamerk22\AmazonGiftCode\Config;


interface ConfigInterface
{
    /**
     * @return String
     */
    public function getEndpoint();

    /**
     * @param $endpoint
     * @return $this
     */
    public function setEndpoint($endpoint);

    /**
     * @return String
     */
    public function getAccessKey();

    /**
     * @param $key
     * @return $this
     */
    public function setAccessKey($key);

    /**
     * @return String
     */
    public function getSecret();

    /**
     * @param $secret
     * @return $this
     */
    public function setSecret($secret);

    /**
     * @return String
     */
    public function getCurrency();

    /**
     * @param $currency
     * @return $this
     */
    public function setCurrency($currency);

    /**
     * @return String
     */
    public function getPartner();

    /**
     * @param $partner
     * @return $this
     */
    public function setPartner($partner);
}