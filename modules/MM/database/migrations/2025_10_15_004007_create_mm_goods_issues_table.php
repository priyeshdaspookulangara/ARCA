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
        Schema::create('mm_goods_issues', function (Blueprint $table) {
            $table->id();
            $table->string('issue_type'); // POS_SALE, INTERNAL_CONSUMPTION, SCRAPPING
            $table->unsignedBigInteger('reference_id')->nullable(); // e.g., sales_order_id
            $table->date('issue_date');
            $table->string('status'); // POSTED, CANCELLED
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('mm_goods_issue_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('goods_issue_id');
            $table->unsignedBigInteger('material_id');
            $table->decimal('quantity_issued', 15, 2);
            $table->timestamps();

            $table->foreign('goods_issue_id')->references('id')->on('mm_goods_issues')->onDelete('cascade');
            $table->foreign('material_id')->references('id')->on('mm_materials');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mm_goods_issue_items');
        Schema::dropIfExists('mm_goods_issues');
    }
};