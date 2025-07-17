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
        Schema::create('hr_employee_work_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hr_employees');
            $table->date('valid_from');
            $table->date('valid_to');
            $table->foreignId('action_request_id_triggered_by')->constrained('hr_personnel_action_requests');
            $table->foreignId('employment_type_id')->constrained('hr_employment_types');
            $table->foreignId('work_schedule_rule_id')->constrained('hr_work_schedule_rules');
            $table->decimal('weekly_hours', 5, 2);
            $table->decimal('fte_percentage', 5, 2);
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
        Schema::dropIfExists('hr_employee_work_schedules');
    }
};
