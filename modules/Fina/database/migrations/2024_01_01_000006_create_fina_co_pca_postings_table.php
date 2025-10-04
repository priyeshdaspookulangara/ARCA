<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fina_co_pca_postings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('profit_center_id');
            $table->unsignedBigInteger('gl_account_id');
            $table->string('document_number');
            $table->decimal('amount', 15, 2);
            $table->date('posting_date');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('profit_center_id')->references('id')->on('fina_co_pca_profit_centers')->onDelete('cascade');
            $table->foreign('gl_account_id')->references('id')->on('fina_gl_accounts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fina_co_pca_postings');
    }
};