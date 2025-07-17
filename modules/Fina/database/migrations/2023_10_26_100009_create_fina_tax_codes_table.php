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
        Schema::create('fina_tax_codes', function (Blueprint $table) {
            $table->id();
            $table->string('country_code');
            $table->string('tax_code');
            $table->string('description');
            $table->enum('tax_type', ['Input', 'Output']);
            $table->decimal('tax_rate_percentage', 15, 6);
            $table->unsignedBigInteger('gl_account_id_for_posting');
            $table->timestamps();

            $table->unique(['country_code', 'tax_code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fina_tax_codes');
    }
};
