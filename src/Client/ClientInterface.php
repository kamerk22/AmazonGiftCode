<?php

/**
 * Part of the AmazonGiftCode package.
 * Author: Kashyap Merai <kashyapk62@gmail.com>
 *
 */


namespace kamerk22\AmazonGiftCode\Client;


interface ClientInterface
{
    /**
     * @param string $url The URL being requested, including domain and protocol
     * @param array $headers Headers to be used in the request
     * @param array $params Can be nested for arrays and hashes
     *
     *
     * @return array An array whose first element is raw request body, second
     *    element is HTTP status code and third array of HTTP headers.
     */

    public function request($url, $headers, $params);
}