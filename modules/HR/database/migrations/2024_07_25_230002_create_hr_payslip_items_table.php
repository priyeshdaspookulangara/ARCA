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
        Schema::create('hr_payslip_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hr_payslip_id')->constrained('hr_payslips')->onDelete('cascade');

            $table->enum('item_type', ['earning', 'deduction']);
            $table->string('description');
            $table->decimal('amount', 15, 2);

            $table->boolean('is_pre_tax')->default(true)->comment('Applies to deductions: true for pre-tax, false for post-tax');
            // Could add a 'code' field for specific earning/deduction codes if needed
            // $table->string('code')->nullable();

            $table->timestamps();
            // No soft deletes needed for items, they are deleted with the payslip
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_payslip_items');
    }
};
