<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gachapon_claims', function (Blueprint $table) {
            $table->id();

            $table->foreignId('machine_id')
                ->constrained('gachapon_machines')
                ->cascadeOnDelete();

            $table->foreignId('gift_id')
                ->constrained('gachapon_gifts')
                ->cascadeOnDelete();

            $table->string('claim_reference', 64)->unique();
            $table->timestamp('revealed_at')->nullable();

            $table->timestamps();

            $table->index(['machine_id', 'gift_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gachapon_claims');
    }
};