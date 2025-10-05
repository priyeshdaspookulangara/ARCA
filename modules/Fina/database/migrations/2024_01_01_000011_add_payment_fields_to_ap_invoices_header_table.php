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
        Schema::table('fina_ap_invoices_header', function (Blueprint $table) {
            $table->string('payment_block')->nullable()->after('payment_status');
            $table->unsignedBigInteger('payment_run_id')->nullable()->after('payment_block');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fina_ap_invoices_header', function (Blueprint $table) {
            $table->dropColumn('payment_block');
            $table->dropColumn('payment_run_id');
        });
    }
};