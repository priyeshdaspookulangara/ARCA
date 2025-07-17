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
        Schema::create('fina_co_cost_centers_master', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('controlling_area_id');
            $table->string('cost_center_code');
            $table->string('name');
            $table->date('valid_from_date');
            $table->date('valid_to_date');
            $table->unsignedBigInteger('person_responsible_user_id');
            $table->unsignedBigInteger('hierarchy_node_id');
            $table->unsignedBigInteger('company_code_id');
            $table->unsignedBigInteger('profit_center_id')->nullable();
            $table->timestamps();

            $table->unique(['controlling_area_id', 'cost_center_code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fina_co_cost_centers_master');
    }
};
