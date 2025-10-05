<?php

namespace Tests\Feature;

use Tests\TestCase;
use Modules\HR\PersonnelAdmin\Domain\Repositories\EmployeeRepositoryInterface;
use Modules\Fina\FI\AP\Domain\Ledger\FinaPayrollLedgerInterface;

class HrModuleTest extends TestCase
{
    public function test_salary_change_endpoint_updates_hr_repo_and_fina_ledger()
    {
        $finaLedger = $this->app->make(FinaPayrollLedgerInterface::class);
        $initialFinaRecord = $finaLedger->getEmployeeRecord('123');
        $this->assertEquals(50000, $initialFinaRecord['salary']);

        $response = $this->postJson('/api/employees/123/salary', ['new_salary' => 55000]);

        $response->assertStatus(200)->assertJsonFragment(['salary' => 55000]);

        $hrRepository = $this->app->make(EmployeeRepositoryInterface::class);
        $hrEmployee = $hrRepository->findById('123');
        $this->assertEquals(55000, $hrEmployee->getSalary());

        $updatedFinaRecord = $finaLedger->getEmployeeRecord('123');
        $this->assertEquals(55000, $updatedFinaRecord['salary']);
    }

    public function test_personal_data_update_endpoint_updates_hr_repo_and_fina_ledger()
    {
        $finaLedger = $this->app->make(FinaPayrollLedgerInterface::class);
        $initialFinaRecord = $finaLedger->getEmployeeRecord('456');
        $this->assertEquals('{"account":"222","bank":"Bank B"}', $initialFinaRecord['bank_details']);

        $updateData = [
            'address' => '789 New St',
            'bank_details' => ['account' => '333', 'bank' => 'Bank C'],
        ];
        $response = $this->putJson('/api/employees/456/personal-data', $updateData);

        $response->assertStatus(200)->assertJsonFragment(['address' => '789 New St']);

        $hrRepository = $this->app->make(EmployeeRepositoryInterface::class);
        $hrEmployee = $hrRepository->findById('456');
        $this->assertEquals('789 New St', $hrEmployee->getAddress());
        $this->assertEquals('{"account":"333","bank":"Bank C"}', $hrEmployee->getBankDetails());

        $updatedFinaRecord = $finaLedger->getEmployeeRecord('456');
        $this->assertEquals('{"account":"333","bank":"Bank C"}', $updatedFinaRecord['bank_details']);
    }

    public function test_work_schedule_change_endpoint_updates_hr_repo_and_fina_ledger()
    {
        $finaLedger = $this->app->make(FinaPayrollLedgerInterface::class);
        $initialFinaRecord = $finaLedger->getEmployeeRecord('123');
        $this->assertEquals('Full-Time', $initialFinaRecord['work_schedule']);
        $this->assertEquals('Permanent', $initialFinaRecord['employment_type']);

        $updateData = [
            'work_schedule' => 'Part-Time',
            'employment_type' => 'Contractor',
        ];
        $response = $this->putJson('/api/employees/123/work-schedule', $updateData);

        $response->assertStatus(200)
                 ->assertJsonFragment(['work_schedule' => 'Part-Time'])
                 ->assertJsonFragment(['employment_type' => 'Contractor']);

        $hrRepository = $this->app->make(EmployeeRepositoryInterface::class);
        $hrEmployee = $hrRepository->findById('123');
        $this->assertEquals('Part-Time', $hrEmployee->getWorkSchedule());
        $this->assertEquals('Contractor', $hrEmployee->getEmploymentType());

        $updatedFinaRecord = $finaLedger->getEmployeeRecord('123');
        $this->assertEquals('Part-Time', $updatedFinaRecord['work_schedule']);
        $this->assertEquals('Contractor', $updatedFinaRecord['employment_type']);
    }

    public function test_leave_start_and_end_endpoints_update_status()
    {
        $finaLedger = $this->app->make(FinaPayrollLedgerInterface::class);
        $hrRepository = $this->app->make(EmployeeRepositoryInterface::class);

        // --- Start Leave ---
        $initialFinaRecord = $finaLedger->getEmployeeRecord('123');
        $this->assertFalse($initialFinaRecord['on_leave']);

        $startResponse = $this->postJson('/api/employees/123/leave/start', ['leave_type' => 'Sabbatical']);

        $startResponse->assertStatus(200)
                      ->assertJsonFragment(['on_leave' => true, 'leave_type' => 'Sabbatical']);

        $hrEmployeeOnLeave = $hrRepository->findById('123');
        $this->assertTrue($hrEmployeeOnLeave->isOnLeave());
        $this->assertEquals('Sabbatical', $hrEmployeeOnLeave->getLeaveType());

        $finaRecordOnLeave = $finaLedger->getEmployeeRecord('123');
        $this->assertTrue($finaRecordOnLeave['on_leave']);

        // --- End Leave ---
        $endResponse = $this->postJson('/api/employees/123/leave/end');

        $endResponse->assertStatus(200)
                    ->assertJsonFragment(['on_leave' => false, 'leave_type' => null]);

        $hrEmployeeReturned = $hrRepository->findById('123');
        $this->assertFalse($hrEmployeeReturned->isOnLeave());
        $this->assertNull($hrEmployeeReturned->getLeaveType());

        $finaRecordReturned = $finaLedger->getEmployeeRecord('123');
        $this->assertFalse($finaRecordReturned['on_leave']);
    }
}