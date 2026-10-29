
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gachapon_machines', function (Blueprint $table) {
            $table->unsignedInteger('number_of_godchildren')->default(1);
            $table->unsignedInteger('openings_per_godchild')->default(1);
        });
    }

    public function down(): void
    {
        Schema::table('gachapon_machines', function (Blueprint $table) {
            $table->dropColumn([
                'number_of_godchildren',
                'openings_per_godchild',
            ]);
        });
    }
};
