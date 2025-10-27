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
        Schema::create('tax_ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tax_code_id')->constrained('tax_codes');
            $table->string('period');
            $table->decimal('collected', 15, 2);
            $table->decimal('payable', 15, 2);
            $table->decimal('difference', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_ledgers');
    }
};
