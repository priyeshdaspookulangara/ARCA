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
        Schema::create('hr_employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id_number')->unique()->comment('Company-specific employee ID');

            // Personal Details
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other', 'prefer_not_to_say'])->nullable();
            $table->string('nationality')->nullable();
            $table->string('marital_status')->nullable();

            // Contact Information
            $table->string('personal_email')->unique()->nullable();
            $table->string('work_email')->unique();
            $table->string('phone_mobile')->nullable();
            $table->string('phone_work')->nullable();
            $table->text('address_line_1')->nullable();
            $table->text('address_line_2')->nullable();
            $table->string('city')->nullable();
            $table->string('state_province')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();

            // Employment Details
            $table->foreignId('hr_position_id')->nullable()->constrained('hr_positions')->onDelete('set null');
            $table->foreignId('hr_department_id')->nullable()->constrained('hr_departments')->onDelete('set null'); // Denormalized for easier querying, or derived from position
            $table->date('hire_date');
            $table->date('termination_date')->nullable();
            $table->string('employment_status')->default('active'); // e.g., active, terminated, on_leave
            $table->string('employment_type')->nullable(); // e.g., full-time, part-time, contract

            // Link to core user account (if applicable, assuming a core_users table)
            // $table->foreignId('user_id')->nullable()->unique()->constrained('core_users')->onDelete('set null');

            // Emergency Contact
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            $table->string('emergency_contact_phone')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        // Add constraint for manager_id in hr_departments after hr_employees table is created
        // This avoids a circular dependency if hr_departments migration runs before hr_employees
        // and hr_employees is not yet available for the foreign key.
        // However, since I created hr_departments first, I put the manager_id there.
        // If manager_id was strictly enforced and hr_employees must exist, this would be one way.
        // For now, the manager_id in hr_departments is nullable and might point to an employee.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_employees');
    }
};
