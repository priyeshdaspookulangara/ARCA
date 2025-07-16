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
        Schema::table('hr_payslip_items', function (Blueprint $table) {
            $table->foreignId('earning_type_id')->nullable()->after('hr_payslip_id')->constrained('hr_earning_types')->onDelete('set null');
            $table->foreignId('deduction_type_id')->nullable()->after('earning_type_id')->constrained('hr_deduction_types')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hr_payslip_items', function (Blueprint $table) {
            $table->dropForeign(['earning_type_id']);
            $table->dropForeign(['deduction_type_id']);
            $table->dropColumn(['earning_type_id', 'deduction_type_id']);
        });
    }
};
