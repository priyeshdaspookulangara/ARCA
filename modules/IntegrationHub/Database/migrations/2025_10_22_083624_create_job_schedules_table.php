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
        Schema::create('job_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('job_name');
            $table->string('frequency');
            $table->timestamp('last_run')->nullable();
            $table->timestamp('next_run')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('job_schedules');
    }
};
