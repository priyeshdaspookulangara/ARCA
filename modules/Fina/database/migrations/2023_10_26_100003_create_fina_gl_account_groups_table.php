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
        Schema::create('fina_gl_account_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('chart_of_accounts_id');
            $table->string('group_code');
            $table->string('name');
            $table->string('from_account_number');
            $table->string('to_account_number');
            $table->timestamps();

            $table->unique(['chart_of_accounts_id', 'group_code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fina_gl_account_groups');
    }
};
