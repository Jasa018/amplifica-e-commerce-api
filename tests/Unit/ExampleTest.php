<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HelperTest extends TestCase
{
    use RefreshDatabase;

    public function test_application_environment_is_testing()
    {
        $this->assertEquals('testing', app()->environment());
    }

    public function test_database_connection_works()
    {
        $this->assertDatabaseCount('users', 0);
    }
}
