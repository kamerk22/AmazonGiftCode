<?php

namespace kamerk22\AmazonGiftCode\Tests;

use kamerk22\AmazonGiftCode\AmazonGiftCodeServiceProvider;
use kamerk22\AmazonGiftCode\Facades\AmazonGiftCode;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [AmazonGiftCodeServiceProvider::class];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array<string, class-string>
     */
    protected function getPackageAliases($app): array
    {
        return ['AmazonGiftCode' => AmazonGiftCode::class];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function defineEnvironment($app): void
    {
        $app['config']->set('amazongiftcode.endpoint', 'https://agcod-v2-gamma.amazon.com');
        $app['config']->set('amazongiftcode.key', 'AKIAIOSFODNN7EXAMPLE');
        $app['config']->set('amazongiftcode.secret', 'wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY');
        $app['config']->set('amazongiftcode.partner', 'Examp');
        $app['config']->set('amazongiftcode.currency', 'USD');
    }
}
