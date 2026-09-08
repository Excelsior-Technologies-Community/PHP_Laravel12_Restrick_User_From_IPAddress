<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blocked_ip_logs', function (Blueprint $table) {
            $table->string('country_code', 2)->nullable()->after('user_agent');
            $table->string('city')->nullable()->after('country_code');
            $table->integer('reputation_score')->nullable()->after('city');
            $table->boolean('is_auto_blocked')->default(false)->after('reputation_score');
        });
    }

    public function down(): void
    {
        Schema::table('blocked_ip_logs', function (Blueprint $table) {
            $table->dropColumn(['country_code', 'city', 'reputation_score', 'is_auto_blocked']);
        });
    }
};
