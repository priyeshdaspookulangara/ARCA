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
        Schema::create('hr_payroll_periods', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('e.g., January 2024');
            $table->date('start_date');
            $table->date('end_date');
            $table->date('payment_date')->comment('The date on which salaries for this period are paid');
            $table->string('status')->default('open')->comment('e.g., open, processing, closed, paid');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_payroll_periods');
    }
};
