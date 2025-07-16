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
        Schema::create('hr_leave_accrual_policies', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->foreignId('hr_leave_type_id')->constrained('hr_leave_types')->onDelete('cascade');

            $table->enum('accrual_frequency', ['annually', 'monthly', 'quarterly', 'bi-weekly']);
            $table->decimal('accrual_rate_days', 5, 2)->comment('Days accrued per frequency period');
            $table->decimal('max_carry_over_days', 5, 2)->nullable()->comment('Max days to carry over to next fiscal year');

            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_leave_accrual_policies');
    }
};
