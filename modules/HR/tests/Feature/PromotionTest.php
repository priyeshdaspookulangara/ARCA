<?php

namespace Modules\HR\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\HR\Models\Employee;
use Modules\HR\Models\User;

class PromotionTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_be_promoted()
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->postJson('/api/hr/promote', [
            'employee_id' => $employee->id,
            'effective_date' => '2025-01-01',
            'new_position_id' => 2,
            'new_job_title_id' => 2,
            'new_department_id' => 2,
            'new_cost_center_id' => 2,
            'new_company_code_id' => 2,
            'new_personnel_area_id' => 2,
            'new_personnel_sub_area_id' => 2,
            'new_employee_group_id' => 2,
            'new_employee_sub_group_id' => 2,
            'new_manager_core_user_id' => 2,
            'new_employment_status_id' => 1,
            'new_base_salary_amount' => 60000,
            'new_salary_currency_code' => 'USD',
            'new_pay_frequency' => 'Monthly',
            'new_other_components_json' => null,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('hr_personnel_action_requests', [
            'employee_id' => $employee->id,
            'status' => 'pending_manager_approval',
        ]);
    }
}
