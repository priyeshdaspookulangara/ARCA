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
        Schema::create('hr_personnel_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hr_employee_id')->constrained('hr_employees')->onDelete('cascade');

            $table->string('action_type')->comment('e.g., hire, promotion, transfer, termination, contract_update, salary_change, leave_request');
            $table->date('effective_date');
            $table->text('reason')->nullable();
            $table->json('details_json')->nullable()->comment('Stores action-specific data, e.g., new_position_id, new_salary, old_values');

            $table->string('status')->default('pending')->comment('e.g., pending, approved, executed, rejected, cancelled');

            // Could be foreign keys to a core_users table if a central user system exists
            $table->unsignedBigInteger('created_by_user_id')->nullable()->comment('ID of the user who initiated the action');
            $table->unsignedBigInteger('approved_by_user_id')->nullable()->comment('ID of the user who approved the action');
            $table->timestamp('executed_at')->nullable()->comment('Timestamp when the action was fully processed and applied');

            $table->timestamps();
            $table->softDeletes();

            // Add indexes
            $table->index('hr_employee_id');
            $table->index('action_type');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_personnel_actions');
    }
};
