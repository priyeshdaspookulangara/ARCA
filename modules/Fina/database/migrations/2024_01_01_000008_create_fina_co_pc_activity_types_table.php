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
        Schema::create('fina_co_pc_activity_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('unit'); // e.g., 'hours', 'pieces'
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fina_co_pc_activity_types');
    }
};