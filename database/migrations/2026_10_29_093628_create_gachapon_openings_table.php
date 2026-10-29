<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gachapon_openings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('machine_id')
                ->constrained('gachapon_machines')
                ->cascadeOnDelete();

            $table->foreignId('participant_id')
                ->constrained('gachapon_participants')
                ->cascadeOnDelete();

            $table->foreignId('gift_id')
                ->constrained('gachapon_gifts')
                ->restrictOnDelete();

            $table->unsignedInteger('opening_number');
            $table->timestamp('opened_at');

            $table->timestamps();

            // One opening number per participant.
            $table->unique(['participant_id', 'opening_number']);

            $table->index(['machine_id', 'opened_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gachapon_openings');
    }
};