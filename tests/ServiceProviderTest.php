<?php

namespace kamerk22\AmazonGiftCode\Tests;

use kamerk22\AmazonGiftCode\AmazonGiftCode;
use kamerk22\AmazonGiftCode\Facades\AmazonGiftCode as AmazonGiftCodeFacade;

class ServiceProviderTest extends TestCase
{
    public function test_container_resolves_the_package_singleton(): void
    {
        $this->assertInstanceOf(AmazonGiftCode::class, $this->app->make('amazongiftcode'));
        $this->assertSame($this->app->make('amazongiftcode'), $this->app->make('amazongiftcode'));
    }

    public function test_config_is_merged(): void
    {
        $this->assertSame('USD', config('amazongiftcode.currency'));
    }

    public function test_facade_resolves_to_the_service(): void
    {
        $this->assertInstanceOf(AmazonGiftCode::class, AmazonGiftCodeFacade::getFacadeRoot());
    }

    public function test_make_builds_a_fresh_instance(): void
    {
        $this->assertInstanceOf(AmazonGiftCode::class, AmazonGiftCode::make());
    }
}
