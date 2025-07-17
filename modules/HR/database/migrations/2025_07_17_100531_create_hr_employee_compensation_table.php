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
        Schema::create('hr_employee_compensation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hr_employees');
            $table->date('valid_from');
            $table->date('valid_to');
            $table->foreignId('action_request_id_triggered_by')->constrained('hr_personnel_action_requests');
            $table->decimal('base_salary_amount', 15, 2);
            $table->string('salary_currency_code');
            $table->string('pay_frequency');
            $table->json('other_components_json')->nullable();
            $table->string('reason_for_change_code')->nullable();
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
        Schema::dropIfExists('hr_employee_compensation');
    }
};
