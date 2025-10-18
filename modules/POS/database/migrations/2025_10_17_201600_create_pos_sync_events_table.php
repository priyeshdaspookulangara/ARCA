<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pos_sync_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('pos_sync_batches')->onDelete('cascade');
            $table->uuid('event_id')->unique();
            $table->string('idempotency_key')->unique();
            $table->string('source');
            $table->string('type');
            $table->jsonb('raw_payload');
            $table->jsonb('canonical_payload')->nullable();
            $table->string('status')->default('pending');
            $table->integer('attempts')->default(0);
            $table->timestamp('first_received_at');
            $table->timestamp('last_attempted_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->text('error')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pos_sync_events');
    }
};