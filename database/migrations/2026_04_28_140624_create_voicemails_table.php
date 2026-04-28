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
        Schema::create('voicemails', function (Blueprint $table) {
            $table->id();
            $table->string('mailbox')->unique();
            $table->string('context')->default('default');
            $table->string('password');
            $table->string('fullname')->nullable();
            $table->string('email')->nullable();
            $table->string('pager')->nullable();
            $table->boolean('email_notification')->default(true);
            $table->string('language')->default('en');
            $table->string('timezone')->default('UTC');
            $table->boolean('delete_after_email')->default(false);
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voicemails');
    }
};
