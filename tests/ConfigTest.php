<?php

namespace kamerk22\AmazonGiftCode\Tests;

use kamerk22\AmazonGiftCode\Config\Config;

class ConfigTest extends TestCase
{
    public function test_it_reads_explicit_values_over_config(): void
    {
        $config = new Config('key', 'secret', 'Partn', 'https://agcod-v2.amazon.com', 'EUR');

        $this->assertSame('key', $config->getAccessKey());
        $this->assertSame('secret', $config->getSecret());
        $this->assertSame('Partn', $config->getPartner());
        $this->assertSame('EUR', $config->getCurrency());
    }

    public function test_it_falls_back_to_laravel_config_when_values_are_null(): void
    {
        $config = new Config(null, null, null, null, null);

        $this->assertSame('AKIAIOSFODNN7EXAMPLE', $config->getAccessKey());
        $this->assertSame('wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY', $config->getSecret());
        $this->assertSame('Examp', $config->getPartner());
        $this->assertSame('USD', $config->getCurrency());
    }

    public function test_set_endpoint_extracts_host_from_url(): void
    {
        $config = new Config('k', 's', 'p', 'https://agcod-v2-gamma.amazon.com', 'USD');

        $this->assertSame('agcod-v2-gamma.amazon.com', $config->getEndpoint());
    }

    public function test_setters_are_fluent(): void
    {
        $config = new Config('k', 's', 'p', 'https://agcod-v2.amazon.com', 'USD');

        $this->assertSame($config, $config->setCurrency('CAD'));
        $this->assertSame('CAD', $config->getCurrency());
    }
}
