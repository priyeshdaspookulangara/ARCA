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
        Schema::create('fina_aa_asset_master', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_code_id');
            $table->string('asset_number');
            $table->string('asset_subnumber');
            $table->string('description');
            $table->unsignedBigInteger('asset_class_id');
            $table->date('capitalization_date');
            $table->unsignedBigInteger('cost_center_id');
            $table->integer('quantity')->nullable();
            $table->string('unit_of_measure')->nullable();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->enum('status', ['Active', 'Retired', 'UnderConstruction']);
            $table->timestamps();

            $table->unique(['company_code_id', 'asset_number', 'asset_subnumber']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fina_aa_asset_master');
    }
};
