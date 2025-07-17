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
        Schema::create('fina_co_internal_order_master', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('controlling_area_id');
            $table->string('order_number')->unique();
            $table->string('description');
            $table->unsignedBigInteger('order_type_id');
            $table->unsignedBigInteger('responsible_cost_center_id')->nullable();
            $table->enum('status', ['Created', 'Released', 'Budgeted', 'Closed', 'Settled']);
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
        Schema::dropIfExists('fina_co_internal_order_master');
    }
};
