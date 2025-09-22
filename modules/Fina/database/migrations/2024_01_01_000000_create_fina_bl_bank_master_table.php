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
        Schema::create('fina_bl_bank_master', function (Blueprint $table) {
            $table->id();
            $table->string('bank_name');
            $table->string('bank_key')->unique();
            $table->string('address');
            $table->string('swift_code')->unique();
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
        Schema::dropIfExists('fina_bl_bank_master');
    }
};
