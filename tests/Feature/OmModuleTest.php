<?php

namespace Tests\Feature;

use Tests\TestCase;
use Modules\HR\OrganizationalManagement\Domain\Repositories\OrganizationalUnitRepositoryInterface;
use Modules\HR\OrganizationalManagement\Domain\Repositories\JobRepositoryInterface;
use Modules\HR\OrganizationalManagement\Domain\Repositories\PositionRepositoryInterface;

class OmModuleTest extends TestCase
{
    public function test_can_create_and_get_organizational_unit()
    {
        $response = $this->postJson('/api/om/org-units', ['name' => 'Engineering']);
        $response->assertStatus(201)->assertJsonFragment(['name' => 'Engineering']);
        $id = $response->json('id');

        $getResponse = $this->getJson("/api/om/org-units/{$id}");
        $getResponse->assertStatus(200)->assertJsonFragment(['name' => 'Engineering']);
    }

    public function test_can_create_and_get_job()
    {
        $response = $this->postJson('/api/om/jobs', ['title' => 'Software Developer']);
        $response->assertStatus(201)->assertJsonFragment(['title' => 'Software Developer']);
        $id = $response->json('id');

        $getResponse = $this->getJson("/api/om/jobs/{$id}");
        $getResponse->assertStatus(200)->assertJsonFragment(['title' => 'Software Developer']);
    }

    public function test_can_create_and_get_position()
    {
        $orgUnitResponse = $this->postJson('/api/om/org-units', ['name' => 'Platform Team']);
        $orgUnitId = $orgUnitResponse->json('id');

        $jobResponse = $this->postJson('/api/om/jobs', ['title' => 'Senior Engineer']);
        $jobId = $jobResponse->json('id');

        $response = $this->postJson('/api/om/positions', [
            'job_id' => $jobId,
            'org_unit_id' => $orgUnitId
        ]);
        $response->assertStatus(201)
                 ->assertJsonFragment(['job_id' => $jobId])
                 ->assertJsonFragment(['org_unit_id' => $orgUnitId]);
        $id = $response->json('id');

        $getResponse = $this->getJson("/api/om/positions/{$id}");
        $getResponse->assertStatus(200)
                    ->assertJsonFragment(['job_id' => $jobId])
                    ->assertJsonFragment(['org_unit_id' => $orgUnitId]);
    }
}