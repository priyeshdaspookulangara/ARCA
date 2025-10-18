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
        Schema::create('pos_sync_dlq', function (Blueprint $table) {
            $table->id();
            $table->uuid('event_id');
            $table->jsonb('original_payload');
            $table->jsonb('error_json');
            $table->string('resolution_status')->default('pending');
            $table->string('resolved_by')->nullable();
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
        Schema::dropIfExists('pos_sync_dlq');
    }
};