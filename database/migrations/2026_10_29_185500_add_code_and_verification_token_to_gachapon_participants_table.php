<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gachapon_participants', function (Blueprint $table) {
            $table->string('code', 20)->nullable()->after('name');
            $table->string('verification_token', 64)->nullable()->unique()->after('code');
        });
    }

    public function down(): void
    {
        Schema::table('gachapon_participants', function (Blueprint $table) {
            $table->dropUnique(['verification_token']);
            $table->dropColumn(['code', 'verification_token']);
        });
    }
};
