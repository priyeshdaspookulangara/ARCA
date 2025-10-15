<?php

namespace Tests\Feature;

use Tests\TestCase;
use Modules\HR\TimeManagement\Domain\Repositories\TimeRecordRepositoryInterface;
use Modules\HR\TimeManagement\Domain\Repositories\AbsenceRepositoryInterface;
use Modules\Fina\FI\AP\Domain\Ledger\FinaPayrollLedgerInterface;

use Modules\Fina\Core\Providers\FinaServiceProvider;

class TimeManagementModuleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->app->register(FinaServiceProvider::class);
    }

    public function test_can_record_and_approve_time()
    {
        $finaLedger = $this->app->make(FinaPayrollLedgerInterface::class);
        $initialFinaRecord = $finaLedger->getEmployeeRecord('123');
        $this->assertEquals(0, $initialFinaRecord['worked_hours']);

        // Record time
        $recordResponse = $this->postJson('/api/time/time-records', [
            'employee_id' => '123',
            'date' => '2025-10-27',
            'hours' => 8.0
        ]);
        $recordResponse->assertStatus(201)->assertJsonFragment(['hours' => 8.0, 'status' => 'submitted']);
        $recordId = $recordResponse->json('id');

        // Approve time
        $approveResponse = $this->postJson("/api/time/time-records/{$recordId}/approve");
        $approveResponse->assertStatus(200)->assertJsonFragment(['status' => 'approved']);

        // Verify Fina ledger is updated
        $updatedFinaRecord = $finaLedger->getEmployeeRecord('123');
        $this->assertEquals(8.0, $updatedFinaRecord['worked_hours']);
    }

    public function test_can_request_and_approve_absence()
    {
        $absenceRepository = $this->app->make(AbsenceRepositoryInterface::class);

        // Request absence
        $requestResponse = $this->postJson('/api/time/absences', [
            'employee_id' => '456',
            'absence_type' => 'Vacation',
            'start_date' => '2025-11-10',
            'end_date' => '2025-11-14'
        ]);
        $requestResponse->assertStatus(201)->assertJsonFragment(['absence_type' => 'Vacation', 'status' => 'requested']);
        $absenceId = $requestResponse->json('id');

        // Approve absence
        $approveResponse = $this->postJson("/api/time/absences/{$absenceId}/approve");
        $approveResponse->assertStatus(200)->assertJsonFragment(['status' => 'approved']);

        // Verify absence status in repo
        $approvedAbsence = $absenceRepository->findById($absenceId);
        $this->assertEquals('approved', $approvedAbsence->getStatus());
    }
}