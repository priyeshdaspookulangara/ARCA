<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_employee_job_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hr_employees');
            $table->date('valid_from');
            $table->date('valid_to');
            $table->foreignId('action_request_id_triggered_by')->constrained('hr_personnel_action_requests');
            $table->foreignId('position_id')->constrained('hr_positions');
            $table->foreignId('job_title_id')->constrained('hr_job_titles');
            $table->foreignId('department_id')->constrained('core_organization_units');
            $table->foreignId('cost_center_id')->constrained('fina_co_cost_centers');
            $table->foreignId('company_code_id')->constrained('fina_company_codes');
            $table->foreignId('personnel_area_id')->constrained('hr_personnel_areas');
            $table->foreignId('personnel_sub_area_id')->constrained('hr_personnel_sub_areas');
            $table->foreignId('employee_group_id')->constrained('hr_employee_groups');
            $table->foreignId('employee_sub_group_id')->constrained('hr_employee_sub_groups');
            $table->foreignId('manager_core_user_id')->constrained('users');
            $table->foreignId('employment_status_id')->constrained('hr_employment_statuses');
            $table->string('reason_for_change_code')->nullable();
            $table->timestamps();

            $table->index(['employee_id', 'valid_from', 'valid_to']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hr_employee_job_assignments');
    }
};
