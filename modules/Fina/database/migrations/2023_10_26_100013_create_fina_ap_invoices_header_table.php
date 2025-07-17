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
        Schema::create('fina_ap_invoices_header', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gl_document_header_id');
            $table->unsignedBigInteger('vendor_id');
            $table->string('invoice_number_vendor');
            $table->date('invoice_date');
            $table->date('due_date');
            $table->decimal('gross_amount', 15, 2);
            $table->decimal('net_amount', 15, 2);
            $table->decimal('tax_amount', 15, 2);
            $table->enum('payment_status', ['Open', 'Partially Paid', 'Paid']);
            $table->string('po_number')->nullable();
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
        Schema::dropIfExists('fina_ap_invoices_header');
    }
};
