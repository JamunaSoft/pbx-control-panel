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
        Schema::create('conference_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_number')->unique();
            $table->string('name');
            $table->string('pin')->nullable();
            $table->integer('max_participants')->default(10);
            $table->boolean('recording_enabled')->default(false);
            $table->boolean('wait_for_moderator')->default(false);
            $table->string('moderator_pin')->nullable();
            $table->boolean('mute_on_join')->default(false);
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conference_rooms');
    }
};
