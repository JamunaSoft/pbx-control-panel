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
        Schema::create('cdr', function (Blueprint $table) {
            $table->id();
            $table->string('accountcode')->nullable();
            $table->string('src')->nullable(); // Source (caller)
            $table->string('dst')->nullable(); // Destination (callee)
            $table->string('dcontext')->nullable();
            $table->string('clid')->nullable(); // Caller ID
            $table->string('channel')->nullable();
            $table->string('dstchannel')->nullable();
            $table->string('lastapp')->nullable();
            $table->string('lastdata')->nullable();
            $table->timestamp('start')->nullable();
            $table->timestamp('answer')->nullable();
            $table->timestamp('end')->nullable();
            $table->integer('duration')->default(0);
            $table->integer('billsec')->default(0); // Billable seconds
            $table->string('disposition')->nullable(); // ANSWERED, NO ANSWER, BUSY, etc.
            $table->string('amaflags')->nullable();
            $table->string('uniqueid')->unique();
            $table->string('linkedid')->nullable();
            $table->string('peeraccount')->nullable();
            $table->string('sequence')->nullable();
            $table->timestamps();

            $table->index(['start', 'disposition']);
            $table->index(['src', 'dst']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cdr');
    }
};
