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
        Schema::create('fina_co_pa_profitability_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('market_segment_id');
            $table->decimal('revenue', 15, 2);
            $table->decimal('cost', 15, 2);
            $table->decimal('profit', 15, 2);
            $table->date('period');
            $table->timestamps();

            $table->foreign('market_segment_id')->references('id')->on('fina_co_pa_market_segments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fina_co_pa_profitability_reports');
    }
};