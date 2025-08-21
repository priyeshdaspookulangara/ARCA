<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fina_charts_of_accounts', function (Blueprint $table) {
            $table->unsignedBigInteger('retained_earnings_gl_account_id')->nullable()->after('length_gl_account_number');

            $table->foreign('retained_earnings_gl_account_id')
                  ->references('id')
                  ->on('fina_gl_accounts')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fina_charts_of_accounts', function (Blueprint $table) {
            $table->dropForeign(['retained_earnings_gl_account_id']);
            $table->dropColumn('retained_earnings_gl_account_id');
        });
    }
};
