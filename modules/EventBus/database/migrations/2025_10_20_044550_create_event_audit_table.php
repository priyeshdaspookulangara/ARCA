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
        Schema::create('event_audit', function (Blueprint $table) {
            $table->id();
            $table->string('event_id');
            $table->string('status');
            $table->integer('attempts')->default(0);
            $table->timestamp('last_attempt')->nullable();
            $table->text('error_log')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_audit');
    }
};
