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
        Schema::create('fina_aa_asset_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asset_master_id');
            $table->unsignedBigInteger('depreciation_area_id');
            $table->integer('fiscal_year');
            $table->decimal('acquisition_cost', 15, 2);
            $table->decimal('accumulated_depreciation', 15, 2);
            $table->decimal('planned_depreciation_for_year', 15, 2);
            $table->decimal('net_book_value', 15, 2);
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
        Schema::dropIfExists('fina_aa_asset_values');
    }
};
