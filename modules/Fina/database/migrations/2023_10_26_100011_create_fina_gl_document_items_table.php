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
        Schema::create('fina_gl_document_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_header_id');
            $table->integer('item_number');
            $table->unsignedBigInteger('gl_account_id');
            $table->enum('posting_type', ['Debit', 'Credit']);
            $table->decimal('amount_transaction_currency', 15, 2);
            $table->decimal('amount_local_currency', 15, 2);
            $table->unsignedBigInteger('tax_code_id')->nullable();
            $table->decimal('tax_amount_local_currency', 15, 2)->nullable();
            $table->unsignedBigInteger('cost_center_id')->nullable();
            $table->unsignedBigInteger('internal_order_id')->nullable();
            $table->unsignedBigInteger('profit_center_id')->nullable();
            $table->string('assignment_text')->nullable();
            $table->string('item_text')->nullable();
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
        Schema::dropIfExists('fina_gl_document_items');
    }
};
