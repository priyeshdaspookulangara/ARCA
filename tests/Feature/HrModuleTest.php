<?php

namespace Tests\Feature;

use Tests\TestCase;
use Modules\HR\PersonnelAdmin\Domain\Repositories\EmployeeRepositoryInterface;

class HrModuleTest extends TestCase
{
    public function test_salary_change_endpoint_updates_salary()
    {
        $response = $this->postJson('/api/employees/123/salary', ['new_salary' => 55000]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['salary' => 55000]);

        $repository = $this->app->make(EmployeeRepositoryInterface::class);
        $employee = $repository->findById('123');
        $this->assertEquals(55000, $employee->getSalary());
    }

    public function test_personal_data_update_endpoint_updates_data()
    {
        $updateData = [
            'address' => '789 New St',
            'marital_status' => 'Divorced',
        ];

        $response = $this->putJson('/api/employees/456/personal-data', $updateData);

        $response->assertStatus(200)
                 ->assertJsonFragment(['address' => '789 New St'])
                 ->assertJsonFragment(['marital_status' => 'Divorced']);

        $repository = $this->app->make(EmployeeRepositoryInterface::class);
        $employee = $repository->findById('456');
        $this->assertEquals('789 New St', $employee->getAddress());
        $this->assertEquals('Divorced', $employee->getMaritalStatus());
    }
}