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
        Schema::create('fina_gl_document_headers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_code_id');
            $table->string('document_number');
            $table->integer('fiscal_year');
            $table->string('document_type');
            $table->date('document_date');
            $table->date('posting_date');
            $table->string('reference_text')->nullable();
            $table->string('header_text')->nullable();
            $table->string('transaction_currency_code', 3);
            $table->unsignedBigInteger('created_by_user_id');
            $table->timestamps();

            $table->unique(['company_code_id', 'fiscal_year', 'document_number']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fina_gl_document_headers');
    }
};
