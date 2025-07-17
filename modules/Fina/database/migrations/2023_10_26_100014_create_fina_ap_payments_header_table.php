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
        Schema::create('fina_ap_payments_header', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gl_document_header_id');
            $table->unsignedBigInteger('payment_run_id')->nullable();
            $table->string('payment_method_used');
            $table->unsignedBigInteger('house_bank_account_id');
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
        Schema::dropIfExists('fina_ap_payments_header');
    }
};
