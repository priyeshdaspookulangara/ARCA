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
        Schema::create('fina_ar_receipts_header', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gl_document_header_id');
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
        Schema::dropIfExists('fina_ar_receipts_header');
    }
};
