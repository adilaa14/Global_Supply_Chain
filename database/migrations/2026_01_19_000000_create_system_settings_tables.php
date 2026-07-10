<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // System Settings (Global Configs)
        Schema::create('system_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('setting_key')->unique();
            $table->text('setting_value')->nullable();
            $table->string('category'); // General, Email, Financial, Report, Audit, Backup
            $table->timestamps();
        });

        // API Settings (3rd Party Integrations like AIS, Weather, etc.)
        Schema::create('api_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('provider_name'); // AIS Vessel, OpenWeather, ExchangeRates
            $table->text('api_key')->nullable();
            $table->text('api_secret')->nullable();
            $table->string('webhook_url')->nullable();
            $table->integer('rate_limit')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // AI Settings (Thresholds for Decision Engine)
        Schema::create('ai_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('setting_key')->unique(); // Recommendation Threshold, Risk Threshold
            $table->decimal('numeric_value', 10, 2)->nullable();
            $table->string('string_value')->nullable();
            $table->boolean('boolean_value')->nullable();
            $table->timestamps();
        });
        
        // Extend Users for User Preferences

        Schema::table('users', function (Blueprint $table) {
            $table->string('theme')->default('dark')->after('status');
            $table->string('language', 2)->default('en')->after('theme');
            $table->string('timezone')->default('UTC')->after('language');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['theme', 'language', 'timezone']);
        });
        
        Schema::dropIfExists('ai_settings');
        Schema::dropIfExists('api_settings');
        Schema::dropIfExists('system_settings');
    }
};
