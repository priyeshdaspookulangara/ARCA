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
        Schema::create('fina_ap_payment_proposals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payment_run_id');
            $table->unsignedBigInteger('invoice_id');
            $table->enum('status', ['Proposed', 'Excluded', 'Paid']);
            $table->timestamps();

            $table->foreign('payment_run_id')->references('id')->on('fina_ap_payment_runs')->onDelete('cascade');
            $table->foreign('invoice_id')->references('id')->on('fina_ap_invoices_header')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fina_ap_payment_proposals');
    }
};