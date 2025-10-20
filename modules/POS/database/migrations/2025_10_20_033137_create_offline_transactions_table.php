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
        Schema::create('offline_transactions', function (Blueprint $table) {
            $table->string('transaction_id')->primary();
            $table->timestamp('timestamp');
            $table->string('status')->default('PendingSync');
            $table->text('payload_json');
            $table->integer('sync_attempts')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offline_transactions');
    }
};
