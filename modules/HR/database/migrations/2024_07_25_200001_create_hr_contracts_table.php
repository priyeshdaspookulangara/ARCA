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
        Schema::create('hr_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hr_employee_id')->constrained('hr_employees')->onDelete('cascade');

            $table->string('contract_type')->comment('e.g., permanent, fixed-term, internship, part-time');
            $table->date('start_date');
            $table->date('end_date')->nullable()->comment('Null for permanent contracts or if not yet defined');

            // Snapshots of key details at the time of contract creation/update
            $table->string('job_title_snapshot');
            $table->string('department_snapshot')->nullable();

            $table->decimal('salary_amount', 15, 2); // Allows for larger salaries and precision
            $table->char('salary_currency', 3)->default('USD'); // Default currency, adjust as needed
            $table->string('salary_frequency')->default('monthly')->comment('e.g., hourly, daily, weekly, monthly, annual');

            $table->decimal('working_hours_per_week', 5, 2)->nullable();
            $table->unsignedInteger('probation_period_months')->nullable();
            $table->unsignedInteger('notice_period_days')->nullable();

            $table->string('contract_document_path')->nullable()->comment('Path to the scanned/signed contract document');
            $table->string('status')->default('pending_signature')->comment('e.g., pending_signature, active, expired, terminated_early, superseded');

            $table->text('remarks')->nullable(); // General remarks or notes about the contract

            $table->timestamps();
            $table->softDeletes();

            $table->index('hr_employee_id');
            $table->index('status');
            $table->index('contract_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_contracts');
    }
};
