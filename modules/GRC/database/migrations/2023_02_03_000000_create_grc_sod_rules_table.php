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
        Schema::create('grc_sod_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->json('condition_json');
            $table->string('enforcement_mode')->default('warn'); // warn, block, require_approval
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
        Schema::dropIfExists('grc_sod_rules');
    }
};