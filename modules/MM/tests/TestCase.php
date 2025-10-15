<?php

namespace Modules\MM\Tests;

use Tests\TestCase as BaseTestCase;

use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate:fresh', ['--seed' => true]);
    }
}