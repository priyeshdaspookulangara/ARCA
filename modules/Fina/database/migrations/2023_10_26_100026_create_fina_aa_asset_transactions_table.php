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
        Schema::create('fina_aa_asset_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gl_document_header_id');
            $table->unsignedBigInteger('asset_master_id');
            $table->enum('transaction_type', ['Acquisition', 'Retirement', 'Transfer', 'DepreciationRun']);
            $table->decimal('amount', 15, 2);
            $table->date('posting_date');
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
        Schema::dropIfExists('fina_aa_asset_transactions');
    }
};
