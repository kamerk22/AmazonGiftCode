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
    public function getEndpoint(): string;

    /**
     * @param $endpoint
     * @return $this
     */
    public function setEndpoint($endpoint): ConfigInterface;

    /**
     * @return String
     */
    public function getAccessKey(): string;

    /**
     * @param $key
     * @return $this
     */
    public function setAccessKey($key): ConfigInterface;

    /**
     * @return String
     */
    public function getSecret(): string;

    /**
     * @param $secret
     * @return $this
     */
    public function setSecret($secret): ConfigInterface;

    /**
     * @return String
     */
    public function getCurrency(): string;

    /**
     * @param $currency
     * @return $this
     */
    public function setCurrency($currency): ConfigInterface;

    /**
     * @return String
     */
    public function getPartner(): string;

    /**
     * @param $partner
     * @return $this
     */
    public function setPartner($partner): ConfigInterface;
}