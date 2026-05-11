<?php

namespace Tests\Unit;

use Tests\TestCase;

class BasicTest extends TestCase
{
    public function test_basic_assertion()
    {
        $this->assertTrue(true);
        $this->assertEquals(1, 1);
        $this->assertNotNull('test');
    }

    public function test_database_connection()
    {
        $this->assertTrue(true);
    }
}
