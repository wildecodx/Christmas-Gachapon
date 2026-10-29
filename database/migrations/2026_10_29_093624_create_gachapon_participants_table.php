<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gachapon_participants', function (Blueprint $table) {
            $table->id();

            $table->foreignId('machine_id')
                ->constrained('gachapon_machines')
                ->cascadeOnDelete();

            $table->string('name');

            // Store only a hash of the personal invitation code.
            $table->string('code_hash', 64)->unique();

            // Null means unlimited openings, subject to available gifts.
            $table->unsignedInteger('max_openings')->nullable();

            $table->unsignedInteger('openings_used')->default(0);

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['machine_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gachapon_participants');
    }
};