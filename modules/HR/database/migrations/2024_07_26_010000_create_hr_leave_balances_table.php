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
        Schema::create('hr_leave_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hr_employee_id')->constrained('hr_employees')->onDelete('cascade');
            $table->foreignId('hr_leave_type_id')->constrained('hr_leave_types')->onDelete('cascade');

            $table->year('fiscal_year');
            $table->decimal('entitlement_days', 5, 2)->comment('Total days allocated for the year');
            $table->decimal('taken_days', 5, 2)->default(0.00)->comment('Days already used');
            $table->decimal('balance_days', 5, 2)->storedAs('entitlement_days - taken_days')->comment('Calculated balance');

            $table->text('notes')->nullable()->comment('Notes about this specific balance, e.g., manual adjustments');

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['hr_employee_id', 'hr_leave_type_id', 'fiscal_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_leave_balances');
    }
};
