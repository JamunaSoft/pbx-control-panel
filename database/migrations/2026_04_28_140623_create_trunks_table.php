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
        Schema::create('trunks', function (Blueprint $table) {
            $table->id();
            $table->string('trunk_name')->unique();
            $table->string('provider');
            $table->string('host');
            $table->string('username')->nullable();
            $table->string('secret')->nullable();
            $table->string('context')->default('from-trunk');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->enum('type', ['sip', 'iax', 'pjsip'])->default('sip');
            $table->integer('port')->default(5060);
            $table->boolean('failover_enabled')->default(false);
            $table->json('failover_trunks')->nullable();
            $table->decimal('cost_per_minute', 8, 4)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trunks');
    }
};
