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
        Schema::create('hr_employee_personal_data_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hr_employees');
            $table->date('valid_from');
            $table->date('valid_to');
            $table->foreignId('action_request_id_triggered_by')->constrained('hr_personnel_action_requests');
            $table->string('last_name');
            $table->string('first_name');
            $table->foreignId('marital_status_id')->constrained('hr_marital_statuses');
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->text('bank_account_details_json_encrypted')->nullable();
            $table->timestamps();

            $table->index(['employee_id', 'valid_from', 'valid_to']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hr_employee_personal_data_versions');
    }
};
