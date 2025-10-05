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
        Schema::create('fina_ap_payment_runs', function (Blueprint $table) {
            $table->id();
            $table->date('run_date');
            $table->enum('status', ['Proposal Created', 'Payments Executed', 'Cancelled']);
            $table->text('parameters'); // JSON field for storing payment run parameters
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
        Schema::dropIfExists('fina_ap_payment_runs');
    }
};