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
        Schema::create('extensions', function (Blueprint $table) {
            $table->id();
            $table->string('extension_number')->unique();
            $table->string('password');
            $table->string('display_name');
            $table->string('email')->nullable();
            $table->enum('status', ['online', 'offline', 'ringing', 'busy'])->default('offline');
            $table->enum('device_type', ['sip', 'iax', 'pjsip'])->default('sip');
            $table->string('context')->default('default');
            $table->boolean('call_forwarding_enabled')->default(false);
            $table->string('call_forwarding_number')->nullable();
            $table->boolean('dnd_enabled')->default(false);
            $table->boolean('voicemail_enabled')->default(true);
            $table->integer('voicemail_box')->nullable();
            $table->string('ring_group')->nullable();
            $table->json('follow_me_numbers')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extensions');
    }
};
