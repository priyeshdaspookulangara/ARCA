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
        Schema::table('fina_gl_document_headers', function (Blueprint $table) {
            $table->string('reversal_reason', 10)->nullable()->after('header_text');
            $table->date('reversal_date')->nullable()->after('reversal_reason');
            $table->unsignedBigInteger('reversed_by_document_id')->nullable()->after('reversal_date');
            $table->unsignedBigInteger('reverses_document_id')->nullable()->after('reversed_by_document_id');

            // It's good practice to add an index for foreign keys
            $table->foreign('reversed_by_document_id')->references('id')->on('fina_gl_document_headers');
            $table->foreign('reverses_document_id')->references('id')->on('fina_gl_document_headers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fina_gl_document_headers', function (Blueprint $table) {
            // Drop foreign keys before dropping columns
            $table->dropForeign(['reversed_by_document_id']);
            $table->dropForeign(['reverses_document_id']);

            $table->dropColumn('reversal_reason');
            $table->dropColumn('reversal_date');
            $table->dropColumn('reversed_by_document_id');
            $table->dropColumn('reverses_document_id');
        });
    }
};
