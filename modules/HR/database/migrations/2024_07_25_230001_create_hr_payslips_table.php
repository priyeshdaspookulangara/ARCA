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
        Schema::create('hr_payslips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hr_employee_id')->constrained('hr_employees')->onDelete('cascade');
            $table->foreignId('hr_payroll_period_id')->constrained('hr_payroll_periods')->onDelete('cascade');

            $table->decimal('gross_salary', 15, 2);
            $table->decimal('total_deductions', 15, 2);
            $table->decimal('net_salary', 15, 2);

            $table->string('status')->default('draft')->comment('e.g., draft, confirmed, paid, rejected');
            $table->text('notes')->nullable()->comment('Admin notes about this specific payslip');

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['hr_employee_id', 'hr_payroll_period_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_payslips');
    }
};
