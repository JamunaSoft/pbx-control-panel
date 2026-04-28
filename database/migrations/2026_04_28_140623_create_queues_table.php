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
        Schema::create('queues', function (Blueprint $table) {
            $table->id();
            $table->string('queue_name')->unique();
            $table->enum('strategy', ['ringall', 'leastrecent', 'fewestcalls', 'random', 'rrmemory', 'linear'])->default('ringall');
            $table->integer('timeout')->default(15);
            $table->integer('wrapuptime')->default(0);
            $table->integer('maxlen')->default(0); // 0 = unlimited
            $table->string('announce')->nullable();
            $table->string('context')->default('default');
            $table->boolean('enabled')->default(true);
            $table->integer('servicelevel')->default(0);
            $table->string('musicclass')->default('default');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queues');
    }
};
