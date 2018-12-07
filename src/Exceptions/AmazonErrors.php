<?php

/**
 * Part of the AmazonGiftCode package.
 * Author: Kashyap Merai <kashyapk62@gmail.com>
 *
 */


namespace kamerk22\AmazonGiftCode\Exceptions;


use RuntimeException;

class AmazonErrors extends RuntimeException
{
    /**
     * @param string $message
     * @param string $_error_code
     * @return AmazonErrors
     */
    public static function getError(string $message, string $_error_code): self
    {
        return new static($message, $_error_code);
    }

}