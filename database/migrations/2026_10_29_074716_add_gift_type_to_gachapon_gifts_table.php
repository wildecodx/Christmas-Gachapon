<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columnExists = DB::select(
            "SHOW COLUMNS FROM `gachapon_gifts` LIKE 'gift_type'"
        );

        if (empty($columnExists)) {
            Schema::table('gachapon_gifts', function (Blueprint $table) {
                $table->string('gift_type', 20)->default('custom');
            });
        }
    }

    public function down(): void
    {
        // Keep the existing gift_type column and its data.
    }
};