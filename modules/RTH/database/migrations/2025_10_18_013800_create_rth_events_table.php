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
        Schema::create('rth_events', function (Blueprint $table) {
            $table->id();
            $table->uuid('event_id')->unique();
            $table->string('source');
            $table->string('type');
            $table->jsonb('canonical_payload');
            $table->string('status')->default('received');
            $table->integer('attempts')->default(0);
            $table->timestamp('first_received_at')->useCurrent();
            $table->timestamp('last_attempted_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->string('idempotency_key')->unique();
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
        Schema::dropIfExists('rth_events');
    }
};