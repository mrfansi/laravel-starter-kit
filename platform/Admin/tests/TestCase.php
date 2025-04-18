<?php

namespace Platform\Admin\Tests;

use Platform\Admin\Models\FactoryResolution;
use Tests\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Bootstrap the factory resolution for Admin platform models
        FactoryResolution::bootstrap();
    }
}
