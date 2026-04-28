<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ivrs', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('greeting_audio')->nullable();
            $table->string('timeout_action')->default('hangup');
            $table->integer('timeout_seconds')->default(10);
            $table->json('menu_options'); // JSON structure for menu options
            $table->string('invalid_input_action')->default('repeat');
            $table->integer('max_attempts')->default(3);
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ivrs');
    }
};
