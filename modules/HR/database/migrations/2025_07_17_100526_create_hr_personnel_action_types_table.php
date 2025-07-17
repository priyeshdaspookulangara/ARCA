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
        Schema::create('hr_personnel_action_types', function (Blueprint $table) {
            $table->id();
            $table->string('action_code')->unique();
            $table->string('description');
            $table->string('default_workflow_definition_key');
            $table->boolean('is_ess_allowed')->default(false);
            $table->boolean('is_mss_allowed')->default(false);
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
        Schema::dropIfExists('hr_personnel_action_types');
    }
};
