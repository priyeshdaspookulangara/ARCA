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
        Schema::create('fina_co_pa_profitability_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('market_segment_id')->constrained('fina_co_pa_market_segments');
            $table->date('period_start_date');
            $table->date('period_end_date');
            $table->decimal('revenue', 15, 2);
            $table->decimal('cost_of_sales', 15, 2);
            $table->decimal('gross_profit', 15, 2);
            $table->json('detailed_costs')->nullable(); // e.g., marketing, administrative
            $table->decimal('net_profit', 15, 2);
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
        Schema::dropIfExists('fina_co_pa_profitability_reports');
    }
};