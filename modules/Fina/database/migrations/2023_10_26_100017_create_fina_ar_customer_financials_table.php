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
        Schema::create('fina_ar_customer_financials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('company_code_id');
            $table->unsignedBigInteger('reconciliation_gl_account_id');
            $table->unsignedBigInteger('payment_terms_id');
            $table->decimal('credit_limit', 15, 2);
            $table->unsignedBigInteger('dunning_procedure_id');
            $table->timestamps();

            $table->unique(['customer_id', 'company_code_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fina_ar_customer_financials');
    }
};
