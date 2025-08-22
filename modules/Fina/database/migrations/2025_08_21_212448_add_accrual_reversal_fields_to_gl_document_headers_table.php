<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::table('fina_gl_document_headers', function (Blueprint $table) {
            $table->boolean('is_reversing_entry')->default(false)->after('reverses_document_id');
            $table->date('reverses_on_date')->nullable()->after('is_reversing_entry');
        });
    }

    public function down()
    {
        Schema::table('fina_gl_document_headers', function (Blueprint $table) {
            $table->dropColumn(['is_reversing_entry', 'reverses_on_date']);
        });
    }
};
