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
        Schema::create('fina_ar_receipt_invoice_links', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('receipt_header_id');
            $table->unsignedBigInteger('invoice_header_id');
            $table->decimal('cleared_amount', 15, 2);
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
        Schema::dropIfExists('fina_ar_receipt_invoice_links');
    }
};
