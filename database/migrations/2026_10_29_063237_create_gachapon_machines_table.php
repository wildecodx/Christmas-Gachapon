<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gachapon_machines', function (Blueprint $table) {
            $table->id();

            $table->string('name')->default('Christmas Gachapon');
            $table->string('godparent_name')->nullable();
            $table->text('welcome_message')->nullable();

            $table->string('management_token_hash', 64)->unique();
            $table->string('public_token', 64)->unique();
            $table->string('access_code_hash', 255);

            $table->string('status', 20)->default('draft');
            $table->timestamp('opens_at')->nullable();
            $table->timestamp('closes_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gachapon_machines');
    }
};