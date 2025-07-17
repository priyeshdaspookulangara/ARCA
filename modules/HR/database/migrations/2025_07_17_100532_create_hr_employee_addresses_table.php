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
        Schema::create('hr_employee_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hr_employees');
            $table->string('address_type');
            $table->date('valid_from');
            $table->date('valid_to');
            $table->foreignId('action_request_id_triggered_by')->constrained('hr_personnel_action_requests');
            $table->string('street');
            $table->string('city');
            $table->string('postal_code');
            $table->string('state_or_province');
            $table->string('country_code');
            $table->timestamps();

            $table->index(['employee_id', 'address_type', 'valid_from', 'valid_to']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hr_employee_addresses');
    }
};
