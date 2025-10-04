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
}