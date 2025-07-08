<?php

namespace Modules\HR\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\HR\PersonnelAdmin\Domain\Entities\Employee;
use Modules\HR\PersonnelAdmin\Domain\Entities\Contract;
use Modules\HR\PersonnelAdmin\Domain\Entities\PersonnelAction;
use Tests\TestCase;

class ContractApiTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private Employee $employee;

    protected function setUp(): void
    {
        parent::setUp();
        $this->employee = Employee::factory()->create();
        // Employee factory now includes some contract data for the 'store' method,
        // but for these tests, we'll often create contracts directly.
    }

    private function getValidContractData(array $overrides = []): array
    {
        $jobTitle = $this->employee->position && $this->employee->position->job
                    ? $this->employee->position->job->job_title
                    : $this->faker->jobTitle;
        $departmentName = $this->employee->department
                          ? $this->employee->department->name
                          : $this->faker->company . ' Department';

        return array_merge([
            'contract_type' => Contract::TYPE_FIXED_TERM,
            'start_date' => now()->subMonth()->toDateString(),
            'end_date' => now()->addYear()->toDateString(),
            'job_title_snapshot' => $jobTitle,
            'department_snapshot' => $departmentName,
            'salary_amount' => $this->faker->numberBetween(50000, 100000),
            'salary_currency' => 'USD',
            'salary_frequency' => Contract::FREQUENCY_ANNUAL,
            'status' => Contract::STATUS_ACTIVE,
        ], $overrides);
    }

    public function test_can_list_employee_contracts()
    {
        Contract::factory()->count(2)->forEmployee($this->employee)->create();
        Contract::factory()->create(); // Contract for another employee

        $response = $this->getJson("/api/hr/employees/{$this->employee->id}/contracts");

        $response->assertStatus(200)
                 ->assertJsonCount(2); // Only contracts for this employee
    }

    public function test_can_create_new_contract_for_employee()
    {
        $data = $this->getValidContractData();
        $response = $this->postJson("/api/hr/employees/{$this->employee->id}/contracts", $data);

        $response->assertStatus(201)
                 ->assertJsonFragment(['contract_type' => $data['contract_type']]);

        $this->assertDatabaseHas('hr_contracts', [
            'hr_employee_id' => $this->employee->id,
            'contract_type' => $data['contract_type']
        ]);
        // Check if a personnel action was logged
        $this->assertDatabaseHas('hr_personnel_actions', [
            'hr_employee_id' => $this->employee->id,
            'action_type' => PersonnelAction::ACTION_TYPE_CONTRACT_UPDATE,
        ]);
    }

    public function test_creating_active_contract_supersedes_other_active_contracts()
    {
        $oldActiveContract = Contract::factory()->forEmployee($this->employee)->active()->create([
            'start_date' => now()->subYear(),
            'end_date' => now()->addYear(), // Clearly active
        ]);

        $newData = $this->getValidContractData([
            'start_date' => now()->subDay()->toDateString(), // Starts before old one ends
            'status' => Contract::STATUS_ACTIVE,
        ]);
        $response = $this->postJson("/api/hr/employees/{$this->employee->id}/contracts", $newData);
        $response->assertStatus(201);

        $oldActiveContract->refresh();
        $this->assertEquals(Contract::STATUS_SUPERSEDED, $oldActiveContract->status);
        // Check if end_date of old contract was updated correctly (should be start_date of new contract)
        $this->assertEquals($newData['start_date'], $oldActiveContract->end_date->toDateString());
    }


    public function test_can_get_a_specific_contract()
    {
        $contract = Contract::factory()->forEmployee($this->employee)->create();
        $response = $this->getJson("/api/hr/contracts/{$contract->id}"); // Using shallow route
        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $contract->id]);
    }

    public function test_can_update_contract_details()
    {
        $contract = Contract::factory()->forEmployee($this->employee)->create();
        $updateData = [
            'remarks' => 'Updated contract terms discussed.',
            'salary_amount' => $contract->salary_amount + 5000,
            'status' => Contract::STATUS_ACTIVE, // Ensure it's active
        ];

        $response = $this->putJson("/api/hr/contracts/{$contract->id}", $updateData);
        $response->assertStatus(200)
                 ->assertJsonFragment(['remarks' => $updateData['remarks']]);
        $this->assertDatabaseHas('hr_contracts', ['id' => $contract->id, 'remarks' => $updateData['remarks']]);
    }

    public function test_update_contract_to_active_supersedes_others()
    {
        $activeContract1 = Contract::factory()->forEmployee($this->employee)->active()->create([
            'start_date' => now()->subMonths(6),
        ]);
        $pendingContract = Contract::factory()->forEmployee($this->employee)
            ->pendingSignature()
            ->create(['start_date' => now()->subMonths(2)]);

        $updateData = [
            'status' => Contract::STATUS_ACTIVE,
            'start_date' => now()->subMonth()->toDateString(), // Ensure this is the latest start_date for active
        ];
        $response = $this->putJson("/api/hr/contracts/{$pendingContract->id}", $updateData);
        $response->assertStatus(200);

        $activeContract1->refresh();
        $this->assertEquals(Contract::STATUS_SUPERSEDED, $activeContract1->status);
        $this->assertEquals($updateData['start_date'], $activeContract1->end_date->toDateString());
    }


    public function test_can_terminate_an_active_contract()
    {
        $contract = Contract::factory()->forEmployee($this->employee)->active()->create();
        $terminationData = [
            'termination_reason' => 'Mutual agreement.',
            'termination_date' => now()->addDays(5)->toDateString(),
        ];

        $response = $this->postJson("/api/hr/contracts/{$contract->id}/terminate", $terminationData);
        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'status' => Contract::STATUS_TERMINATED_EARLY,
                     'end_date' => $terminationData['termination_date']
                 ]);
        $this->assertDatabaseHas('hr_contracts', [
            'id' => $contract->id,
            'status' => Contract::STATUS_TERMINATED_EARLY
        ]);
    }

    public function test_cannot_terminate_already_terminated_or_expired_contract()
    {
        $contract = Contract::factory()->forEmployee($this->employee)->expired()->create();
        $terminationData = [
            'termination_reason' => 'Trying to re-terminate.',
            'termination_date' => now()->toDateString(),
        ];
        $response = $this->postJson("/api/hr/contracts/{$contract->id}/terminate", $terminationData);
        $response->assertStatus(400); // Bad request
    }

}
