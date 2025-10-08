<?php

namespace Tests\Feature;

use Tests\TestCase;
use Modules\HR\Benefits\Domain\Repositories\BenefitPlanRepositoryInterface;
use Modules\HR\Benefits\Domain\Repositories\EmployeeEnrollmentRepositoryInterface;
use Modules\HR\PersonnelAdmin\Domain\Repositories\EmployeeRepositoryInterface;

class BenefitsModuleTest extends TestCase
{
    public function test_enrolling_employee_in_benefit_updates_payroll_deductions()
    {
        // 1. Get initial state
        $employeeRepository = $this->app->make(EmployeeRepositoryInterface::class);
        $employee = $employeeRepository->findById('123');
        $initialDeductions = $employee->getRecurringDeductions();
        $this->assertEquals(0.0, $initialDeductions);

        // 2. Create a Benefit Plan
        $planResponse = $this->postJson('/api/benefits/plans', [
            'name' => 'Health Insurance - Gold',
            'type' => 'Health',
            'deduction_amount' => 150.50
        ]);
        $planResponse->assertStatus(201);
        $planId = $planResponse->json('id');

        // 3. Enroll an Employee in the plan
        $enrollmentResponse = $this->postJson('/api/benefits/enrollments', [
            'employee_id' => '123',
            'plan_id' => $planId
        ]);
        $enrollmentResponse->assertStatus(201);

        // 4. Verify that the Employee's recurring deductions have been updated
        $updatedEmployee = $employeeRepository->findById('123');
        $expectedDeductions = $initialDeductions + 150.50;
        $this->assertEquals($expectedDeductions, $updatedEmployee->getRecurringDeductions());
    }
}