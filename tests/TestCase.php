<?php

namespace S1K3\Bangla\Date\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use S1K3\Bangla\Date\BanglaDateServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Use UTC so timestamp-based assertions are timezone-independent.
        date_default_timezone_set('UTC');
    }

    protected function getPackageProviders($app): array
    {
        return [BanglaDateServiceProvider::class];
    }
}
