<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ip_restrictions', function (Blueprint $table) {
            $table->string('restriction_type', 20)->default('single')->after('is_active');
            $table->string('cidr')->nullable()->after('restriction_type');
            $table->string('country_code', 2)->nullable()->after('cidr');
        });
    }

    public function down(): void
    {
        Schema::table('ip_restrictions', function (Blueprint $table) {
            $table->dropColumn(['restriction_type', 'cidr', 'country_code']);
        });
    }
};
