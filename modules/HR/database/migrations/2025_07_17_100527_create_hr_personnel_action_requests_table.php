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
        Schema::create('hr_personnel_action_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->foreignId('employee_id')->constrained('hr_employees');
            $table->foreignId('action_type_id')->constrained('hr_personnel_action_types');
            $table->date('requested_effective_date');
            $table->text('reason_for_action_text')->nullable();
            $table->string('status');
            $table->foreignId('initiator_user_id')->constrained('users');
            $table->timestamp('submission_datetime')->nullable();
            $table->timestamp('last_approval_datetime')->nullable();
            $table->timestamp('implemented_datetime')->nullable();
            $table->string('workflow_instance_id')->nullable();
            $table->foreignId('current_approver_user_id')->nullable()->constrained('users');
            $table->json('proposed_data_snapshot_json');
            $table->timestamps();
            $table->foreignId('created_by_user_id')->constrained('users');
            $table->foreignId('updated_by_user_id')->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hr_personnel_action_requests');
    }
};
