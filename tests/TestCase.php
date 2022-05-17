<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function assertPreConditions(): void
    {
        $this->assertEquals(
            'testing',
            env('APP_ENV'),
            'Application not running in "testing" environment.'
        );
    }
}
