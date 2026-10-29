<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gachapon_gifts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('machine_id')
                ->constrained('gachapon_machines')
                ->cascadeOnDelete();

            $table->string('name');
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();

            $table->unsignedInteger('quantity');
            $table->unsignedInteger('remaining_quantity');

            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['machine_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gachapon_gifts');
    }
};