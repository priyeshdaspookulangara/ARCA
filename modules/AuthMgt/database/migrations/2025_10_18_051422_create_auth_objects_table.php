<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthObjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auth_objects', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('module');
            $table->text('description')->nullable();
            $table->json('actions'); // e.g., ['create', 'read', 'update', 'delete']
            $table->string('status')->default('active');
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
        Schema::dropIfExists('auth_objects');
    }
}