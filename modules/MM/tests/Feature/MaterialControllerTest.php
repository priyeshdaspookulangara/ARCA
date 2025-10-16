<?php

namespace Modules\MM\Tests\Feature;

use Modules\MM\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MaterialControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_all_materials()
    {
        $this->withoutExceptionHandling();
        $response = $this->get('/api/mm/items');

        $response->assertStatus(200);
    }
}