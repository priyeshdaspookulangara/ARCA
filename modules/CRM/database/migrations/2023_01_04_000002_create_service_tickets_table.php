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
        Schema::create('service_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->string('subject');
            $table->text('description');
            $table->string('status')->default('open'); // e.g., 'open', 'in_progress', 'closed'
            $table->string('priority')->default('medium'); // e.g., 'low', 'medium', 'high'
            $table->unsignedBigInteger('assigned_to')->nullable(); // Foreign key to users table
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
        Schema::dropIfExists('service_tickets');
    }
};