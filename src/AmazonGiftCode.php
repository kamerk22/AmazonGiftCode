<?php

namespace kamerk22\AmazonGiftCode;

use kamerk22\AmazonGiftCode\AWS\AWS;
use kamerk22\AmazonGiftCode\Client\Client;
use kamerk22\AmazonGiftCode\Config\Config;
use SebastianBergmann\Environment\Runtime;

class AmazonGiftCode
{

    private $config;

    public function __construct($key = null, $secret = null, $partner = null, $endpoint = null, $currency = null)
    {
        $this->config = new Config($key, $secret, $partner, $endpoint, $currency);
    }

    public function buyGiftCard(Float $value)
    {
//        if($value <= 0) {
//            throw new \RuntimeException("The gift card value must be greater than 0.");
//        }
        $aws = new AWS($this->config);
        return $aws->getCode($value);
    }

    public static function make($key = null, $secret = null, $partner = null, $endpoint = null, $currency = null)
    {
        return new static($key, $secret, $partner, $endpoint, $currency);
    }

    public function ok()
    {
        return " ok";
    }
}