<?php

namespace kamerk22\AmazonGiftCode\Facades;

use Illuminate\Support\Facades\Facade;

class AmazonGiftCode extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'amazongiftcode';
    }
}
