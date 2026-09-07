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
        Schema::create('blocked_ip_logs', function (Blueprint $table) {
            $table->id();

            $table->string('ip_address');

            $table->text('path')->nullable();

            $table->string('method', 20)->nullable();

            $table->text('user_agent')->nullable();

            $table->dateTime('blocked_at');

            $table->timestamps();

            $table->index('ip_address');
            $table->index('blocked_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blocked_ip_logs');
    }
};