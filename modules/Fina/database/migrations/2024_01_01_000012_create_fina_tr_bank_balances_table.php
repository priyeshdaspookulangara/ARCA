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
        Schema::create('fina_tr_bank_balances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bank_account_id');
            $table->date('balance_date');
            $table->decimal('balance', 15, 2);
            $table->timestamps();

            $table->foreign('bank_account_id')->references('id')->on('fina_bl_bank_accounts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fina_tr_bank_balances');
    }
};