<?php

namespace Modules\HR\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\HR\PersonnelAdmin\Domain\Entities\Job;
use Modules\HR\TalentManagement\Domain\Entities\JobApplication;
use Tests\TestCase;

class JobApplicationApiTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private Job $job;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public'); // Use a fake public disk for file uploads
        $this->job = Job::factory()->create();
    }

    public function test_can_apply_for_a_job_with_resume_upload()
    {
        $applicationData = [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'resume' => UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf'),
            'cover_letter' => $this->faker->paragraph,
        ];

        $response = $this->postJson("/api/hr/jobs/{$this->job->id}/apply", $applicationData);

        $response->assertStatus(201)
                 ->assertJsonFragment(['email' => $applicationData['email']]);

        $application = JobApplication::first();
        $this->assertNotNull($application);
        $this->assertEquals($applicationData['first_name'], $application->first_name);

        // Assert the file was stored
        Storage::disk('public')->assertExists($application->resume_path);
    }

    public function test_apply_for_job_fails_without_required_fields()
    {
        $response = $this->postJson("/api/hr/jobs/{$this->job->id}/apply", []);
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['first_name', 'last_name', 'email', 'resume']);
    }

    public function test_cannot_apply_for_same_job_twice_with_same_email()
    {
        $existingApplication = JobApplication::factory()->forJob($this->job)->create();

        $applicationData = [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $existingApplication->email, // Same email
            'resume' => UploadedFile::fake()->create('resume.pdf', 100),
        ];

        $response = $this->postJson("/api/hr/jobs/{$this->job->id}/apply", $applicationData);
        $response->assertStatus(422)
                 ->assertJsonFragment(['error' => 'You have already applied for this job with this email address.']);
    }


    public function test_admin_can_list_all_job_applications()
    {
        // Assume this endpoint is protected for admins/recruiters
        JobApplication::factory()->count(3)->forJob($this->job)->create();
        JobApplication::factory()->count(2)->create(); // For other jobs

        $response = $this->getJson('/api/hr/job-applications');
        $response->assertStatus(200)
                 ->assertJsonCount(5);
    }

    public function test_admin_can_filter_applications_by_job()
    {
        JobApplication::factory()->count(3)->forJob($this->job)->create();
        JobApplication::factory()->count(2)->create(); // For other jobs

        $response = $this->getJson('/api/hr/job-applications?hr_job_id=' . $this->job->id);
        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    public function test_admin_can_get_a_specific_application()
    {
        $application = JobApplication::factory()->forJob($this->job)->create();
        $response = $this->getJson("/api/hr/job-applications/{$application->id}");
        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $application->id]);
    }

    public function test_admin_can_update_application_status()
    {
        $application = JobApplication::factory()->forJob($this->job)->create(['status' => JobApplication::STATUS_APPLIED]);
        $updateData = ['status' => JobApplication::STATUS_SCREENING];

        $response = $this->putJson("/api/hr/job-applications/{$application->id}", $updateData);
        $response->assertStatus(200)
                 ->assertJsonFragment(['status' => JobApplication::STATUS_SCREENING]);
        $this->assertDatabaseHas('hr_job_applications', ['id' => $application->id, 'status' => JobApplication::STATUS_SCREENING]);
    }

    public function test_admin_can_delete_an_application()
    {
        $application = JobApplication::factory()->forJob($this->job)->create();
        Storage::disk('public')->put($application->resume_path, 'dummy content');

        $response = $this->deleteJson("/api/hr/job-applications/{$application->id}");
        $response->assertStatus(204);

        $this->assertSoftDeleted('hr_job_applications', ['id' => $application->id]);
        Storage::disk('public')->assertMissing($application->resume_path);
    }
}
