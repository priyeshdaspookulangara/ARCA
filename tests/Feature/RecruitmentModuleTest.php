<?php

namespace Tests\Feature;

use Tests\TestCase;
use Modules\HR\Recruitment\Domain\Repositories\JobOpeningRepositoryInterface;
use Modules\HR\Recruitment\Domain\Repositories\ApplicantRepositoryInterface;
use Modules\HR\Recruitment\Domain\Repositories\ApplicationRepositoryInterface;
use Modules\HR\PersonnelAdmin\Domain\Repositories\EmployeeRepositoryInterface;

class RecruitmentModuleTest extends TestCase
{
    public function test_hiring_applicant_creates_employee_record()
    {
        // 1. Create a Job Opening
        $jobOpeningResponse = $this->postJson('/api/recruitment/job-openings', ['position_id' => 'pos_123']);
        $jobOpeningResponse->assertStatus(201);
        $jobOpeningId = $jobOpeningResponse->json('id');

        // 2. Submit an Application
        $applicantData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'phone' => '123-456-7890'
        ];
        $applicationResponse = $this->postJson("/api/recruitment/job-openings/{$jobOpeningId}/applications", $applicantData);
        $applicationResponse->assertStatus(201);
        $applicationId = $applicationResponse->json('id');
        $applicantId = $applicationResponse->json('applicant_id');

        // 3. Hire the Applicant
        $hireResponse = $this->putJson("/api/recruitment/applications/{$applicationId}", ['status' => 'hired']);
        $hireResponse->assertStatus(200)->assertJsonFragment(['status' => 'hired']);

        // 4. Verify that a new Employee record was created
        $employeeRepository = $this->app->make(EmployeeRepositoryInterface::class);
        $newEmployee = $employeeRepository->findById($applicantId);

        $this->assertNotNull($newEmployee, "Employee record was not created for applicant ID: {$applicantId}");
        $this->assertEquals($applicantId, $newEmployee->getId());
        $this->assertEquals('Doe', $newEmployee->getLastName());
    }
}