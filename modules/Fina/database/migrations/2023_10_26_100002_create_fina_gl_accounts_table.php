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
        Schema::create('fina_gl_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('chart_of_accounts_id');
            $table->string('account_number');
            $table->string('name');
            $table->enum('account_type', ['Balance Sheet', 'P&L']);
            $table->unsignedBigInteger('gl_account_group_id');
            $table->enum('is_reconciliation_account_for', ['Vendor', 'Customer', 'Asset', NULL])->nullable();
            $table->unsignedBigInteger('tax_category_id')->nullable();
            $table->boolean('is_balance_only_in_local_currency');
            $table->boolean('is_open_item_managed');
            $table->string('sort_key');
            $table->timestamps();

            $table->unique(['chart_of_accounts_id', 'account_number']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fina_gl_accounts');
    }
};
