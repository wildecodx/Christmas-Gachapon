<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gachapon_machines', function (Blueprint $table) {
            $table->string('godparent_role')
                ->default('godfather')
                ->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('gachapon_machines', function (Blueprint $table) {
            $table->dropColumn('godparent_role');
        });
    }
};