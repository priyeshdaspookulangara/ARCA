<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::table('fina_gl_accounts', function (Blueprint $table) {
            $table->string('classification')->nullable()->after('account_type');
        });
    }

    public function down()
    {
        Schema::table('fina_gl_accounts', function (Blueprint $table) {
            $table->dropColumn('classification');
        });
    }
};
