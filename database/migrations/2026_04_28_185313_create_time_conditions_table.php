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
        Schema::create('time_conditions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->time('time_start')->nullable();
            $table->time('time_end')->nullable();
            $table->json('days_of_week')->nullable(); // 0=Sunday, 1=Monday, etc.
            $table->json('days_of_month')->nullable(); // 1-31
            $table->json('months')->nullable(); // 1-12
            $table->year('year')->nullable();
            $table->boolean('holidays_enabled')->default(false);
            $table->json('holiday_dates')->nullable(); // Format: ['01-01', '12-25']
            $table->string('timezone')->default('UTC');
            $table->string('destination_true')->nullable(); // What to do when condition matches
            $table->string('destination_false')->nullable(); // What to do when condition doesn't match
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_conditions');
    }
};
