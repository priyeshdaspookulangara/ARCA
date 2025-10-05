<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fina_tr_liquidity_forecasts', function (Blueprint $table) {
            $table->id();
            $table->date('forecast_date');
            $table->string('currency');
            $table->decimal('inflows', 15, 2);
            $table->decimal('outflows', 15, 2);
            $table->decimal('net_flow', 15, 2);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fina_tr_liquidity_forecasts');
    }
};