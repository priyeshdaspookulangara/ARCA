<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hr_leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hr_employee_id')->constrained('hr_employees')->onDelete('cascade');
            $table->foreignId('hr_leave_type_id')->constrained('hr_leave_types')->onDelete('restrict'); // Restrict deletion of type if requests exist

            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('duration_days', 4, 2)->comment('Number of leave days requested, allows for half days');
            // Consider adding start_time, end_time or is_half_day (enum: 'full', 'am', 'pm') if more granularity is needed

            $table->text('reason')->nullable();
            $table->string('status')->default('pending')->comment('e.g., pending, approved, rejected, cancelled_by_employee, cancelled_by_admin');

            $table->unsignedBigInteger('approver_user_id')->nullable()->comment('ID of the user (manager/admin) who actioned the request');
            // Potentially: $table->foreign('approver_user_id')->references('id')->on('users');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancelled_by_role')->nullable()->comment('e.g., employee, admin, manager');


            $table->text('employee_remarks')->nullable(); // Additional notes from employee
            $table->text('approver_remarks')->nullable(); // Remarks from the approver

            $table->timestamps();
            $table->softDeletes();

            $table->index(['hr_employee_id', 'start_date']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_leave_requests');
    }
};
