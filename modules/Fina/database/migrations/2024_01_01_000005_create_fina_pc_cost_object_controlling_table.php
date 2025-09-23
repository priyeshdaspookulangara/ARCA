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
        Schema::create('fina_pc_cost_object_controlling', function (Blueprint $table) {
            $table->id();
            $table->string('cost_object');
            $table->string('cost_object_type');
            $table->decimal('planned_costs', 15, 2);
            $table->decimal('actual_costs', 15, 2);
            $table->decimal('variance', 15, 2);
            $table->string('currency');
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
        Schema::dropIfExists('fina_pc_cost_object_controlling');
    }
};
