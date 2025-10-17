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
        Schema::create('loyalty_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loyalty_program_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->integer('min_points');
            $table->decimal('multiplier', 8, 2)->default(1.0);
            $table->timestamps();
        });

        Schema::create('customer_loyalty_tier', function (Blueprint $table) {
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('loyalty_tier_id')->constrained()->onDelete('cascade');
            $table->primary(['customer_id', 'loyalty_tier_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_loyalty_tier');
        Schema::dropIfExists('loyalty_tiers');
    }
};