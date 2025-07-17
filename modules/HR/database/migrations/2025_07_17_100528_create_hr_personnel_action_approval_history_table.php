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
        Schema::create('hr_personnel_action_approval_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('action_request_id')->constrained('hr_personnel_action_requests');
            $table->string('approval_step_name');
            $table->foreignId('approver_user_id')->constrained('users');
            $table->string('decision');
            $table->timestamp('decision_datetime');
            $table->text('comments')->nullable();
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
        Schema::dropIfExists('hr_personnel_action_approval_history');
    }
};
