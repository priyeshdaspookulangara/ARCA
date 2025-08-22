<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fina_gl_recurring_entry_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_code_id');
            $table->string('document_type', 4);
            $table->string('transaction_currency_code', 3);
            $table->string('header_text')->nullable();

            // Scheduling fields
            $table->string('frequency'); // e.g., 'MONTHLY', 'QUARTERLY', 'YEARLY'
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('next_run_date');
            $table->date('last_run_date')->nullable();

            $table->timestamps();

            $table->foreign('company_code_id')->references('id')->on('fina_company_codes');
        });

        Schema::create('fina_gl_recurring_entry_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('recurring_document_id');
            $table->unsignedBigInteger('gl_account_id');
            $table->enum('posting_type', ['Debit', 'Credit']);
            $table->decimal('amount_transaction_currency', 15, 2);
            $table->string('item_text')->nullable();

            $table->timestamps();

            $table->foreign('recurring_document_id')->references('id')->on('fina_gl_recurring_entry_documents')->onDelete('cascade');
            $table->foreign('gl_account_id')->references('id')->on('fina_gl_accounts');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fina_gl_recurring_entry_items');
        Schema::dropIfExists('fina_gl_recurring_entry_documents');
    }
};
