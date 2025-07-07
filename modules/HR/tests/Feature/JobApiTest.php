<?php

namespace Modules\HR\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\HR\PersonnelAdmin\Domain\Entities\Job;
use Modules\HR\PersonnelAdmin\Domain\Entities\Position; // To test restriction on delete
use Tests\TestCase;

class JobApiTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    // No specific setUp needed beyond RefreshDatabase for now

    public function test_can_get_all_jobs()
    {
        Job::factory()->count(3)->create();

        $response = $this->getJson('/api/hr/jobs');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    public function test_can_create_job()
    {
        $data = [
            'job_title' => $this->faker->unique()->jobTitle,
            'job_description' => $this->faker->sentence,
            'job_code' => $this->faker->unique()->bothify('JC###??'),
            'min_salary' => 50000,
            'max_salary' => 80000,
        ];

        $response = $this->postJson('/api/hr/jobs', $data);

        $response->assertStatus(201)
                 ->assertJsonFragment(['job_title' => $data['job_title']]);
        $this->assertDatabaseHas('hr_jobs', ['job_title' => $data['job_title']]);
    }

    public function test_create_job_validates_salary_range()
    {
        $data = [
            'job_title' => $this->faker->unique()->jobTitle,
            'min_salary' => 80000,
            'max_salary' => 50000, // Invalid: min > max
        ];

        $response = $this->postJson('/api/hr/jobs', $data);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['min_salary']); // or max_salary depending on exact validation logic
    }

    public function test_create_job_fails_with_duplicate_job_title()
    {
        $existingJob = Job::factory()->create();
        $data = [
            'job_title' => $existingJob->job_title, // Duplicate title
        ];

        $response = $this->postJson('/api/hr/jobs', $data);
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['job_title']);
    }


    public function test_can_get_a_job()
    {
        $job = Job::factory()->create();
        $response = $this->getJson("/api/hr/jobs/{$job->id}");
        $response->assertStatus(200)
                 ->assertJsonFragment(['job_title' => $job->job_title]);
    }

    public function test_can_update_job()
    {
        $job = Job::factory()->create();
        $updatedData = [
            'job_title' => 'Updated Job Title - ' . $this->faker->word,
            'job_description' => 'Updated job description.',
            'min_salary' => 60000,
        ];

        $response = $this->putJson("/api/hr/jobs/{$job->id}", $updatedData);

        $response->assertStatus(200)
                 ->assertJsonFragment(['job_title' => $updatedData['job_title']]);
        $this->assertDatabaseHas('hr_jobs', ['id' => $job->id, 'job_title' => $updatedData['job_title']]);
    }

    public function test_update_job_validates_salary_range_when_only_min_is_changed()
    {
        $job = Job::factory()->create(['min_salary' => 50000, 'max_salary' => 80000]);
        $updatedData = [
            'job_title' => $job->job_title, // Need to pass required fields for update
            'min_salary' => 90000, // New min_salary is greater than existing max_salary
        ];

        $response = $this->putJson("/api/hr/jobs/{$job->id}", $updatedData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['min_salary']);
    }

    public function test_can_delete_job()
    {
        $job = Job::factory()->create();
        $response = $this->deleteJson("/api/hr/jobs/{$job->id}");
        $response->assertStatus(204);
        $this->assertSoftDeleted('hr_jobs', ['id' => $job->id]);
    }

    public function test_cannot_delete_job_if_associated_with_positions()
    {
        // This test requires PositionFactory and ability to link Job to Position
        // For now, this is a conceptual test. Implementation will require Position model & factory.
        $job = Job::factory()->create();

        // Create a dummy Department for the Position, as it's a required field.
        // This assumes DepartmentFactory exists and is functional.
        $department = \Modules\HR\PersonnelAdmin\Domain\Entities\Department::factory()->create();

        Position::factory()->create([
            'hr_job_id' => $job->id,
            'hr_department_id' => $department->id, // Position needs a department
            'position_title' => 'Some Position for ' . $job->job_title,
        ]);

        $response = $this->deleteJson("/api/hr/jobs/{$job->id}");

        $response->assertStatus(422)
                 ->assertJsonFragment(['error' => 'Cannot delete job title with associated positions. Reassign or delete positions first.']);
        $this->assertNotSoftDeleted('hr_jobs', ['id' => $job->id]);
    }
}
