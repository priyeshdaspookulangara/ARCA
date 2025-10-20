<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auth_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique(); // Link to the main users table
            $table->timestamps();

            // Foreign key constraint to the main users table
            // Note: The 'users' table is assumed to exist in the global scope.
            // If it's in a different database, this might need adjustment.
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('auth_users');
    }
}