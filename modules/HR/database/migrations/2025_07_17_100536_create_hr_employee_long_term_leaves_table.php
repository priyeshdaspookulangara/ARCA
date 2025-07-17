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
        Schema::create('hr_employee_long_term_leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hr_employees');
            $table->foreignId('action_request_id_start')->constrained('hr_personnel_action_requests');
            $table->foreignId('action_request_id_end')->nullable()->constrained('hr_personnel_action_requests');
            $table->foreignId('leave_type_id')->constrained('hr_leave_types');
            $table->date('planned_start_date');
            $table->date('actual_start_date')->nullable();
            $table->date('expected_return_date')->nullable();
            $table->date('actual_return_date')->nullable();
            $table->string('status');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hr_employee_long_term_leaves');
    }
};
