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
        Schema::create('call_routes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('pattern'); // Asterisk dialplan pattern
            $table->string('destination_type'); // extension, queue, ivr, trunk
            $table->string('destination_value');
            $table->integer('priority')->default(1);
            $table->string('context')->default('default');
            $table->boolean('enabled')->default(true);
            $table->json('time_conditions')->nullable(); // For business hours, holidays, etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('call_routes');
    }
};
