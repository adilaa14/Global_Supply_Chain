<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Currency Rates (Historical and Live)
        Schema::create('currency_rates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('currency_id')->constrained('currencies')->cascadeOnDelete();
            $table->string('base_currency', 3)->default('USD');
            $table->decimal('exchange_rate', 15, 6);
            $table->decimal('daily_change', 5, 2)->default(0);
            $table->decimal('weekly_change', 5, 2)->default(0);
            $table->decimal('monthly_change', 5, 2)->default(0);
            $table->decimal('yearly_change', 5, 2)->default(0);
            $table->decimal('volatility_index', 5, 2)->default(0);
            $table->timestamp('recorded_at');
            $table->timestamps();
        });

        // Currency Forecasts
        Schema::create('currency_forecasts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('currency_id')->constrained('currencies')->cascadeOnDelete();
            $table->string('forecast_period'); // 7 Days, 30 Days
            $table->decimal('predicted_rate', 15, 6);
            $table->string('trend'); // Up, Down, Stable
            $table->integer('confidence_score')->default(0);
            $table->timestamps();
        });

        // Weather Reports
        Schema::create('weather_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->decimal('latitude', 10, 6)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();
            $table->foreignUuid('port_id')->nullable()->constrained('ports')->nullOnDelete();
            $table->foreignUuid('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->decimal('temperature', 5, 2)->default(0);
            $table->decimal('humidity', 5, 2)->default(0);
            $table->decimal('wind_speed', 5, 2)->default(0); // Knots
            $table->decimal('wave_height', 5, 2)->default(0); // Meters
            $table->string('visibility')->nullable();
            $table->integer('storm_probability')->default(0);
            $table->string('condition'); // Clear, Rain, Storm, Cyclone
            $table->timestamp('recorded_at');
            $table->timestamps();
        });

        // Weather Alerts
        Schema::create('weather_alerts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('alert_type'); // Typhoon, Cyclone, Flood
            $table->text('description');
            $table->string('severity'); // Warning, Critical
            $table->decimal('latitude', 10, 6)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();
            $table->foreignUuid('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        // Risk Events (Wars, Pandemics, Strikes)
        Schema::create('risk_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('event_type'); // Political, Military, Natural Disaster
            $table->string('title');
            $table->text('description');
            $table->integer('impact_score')->default(0);
            $table->foreignUuid('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->string('status'); // Active, Resolved
            $table->timestamps();
        });

        // Global Risks (Aggregate of all risks)
        Schema::create('global_risks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('political_score')->default(0);
            $table->integer('economic_score')->default(0);
            $table->integer('weather_score')->default(0);
            $table->integer('currency_score')->default(0);
            $table->integer('shipping_score')->default(0);
            $table->integer('security_score')->default(0);
            $table->integer('trade_score')->default(0);
            $table->integer('overall_risk_score')->default(0);
            $table->timestamp('calculated_at');
            $table->timestamps();
        });

        // Trade Restrictions
        Schema::create('trade_restrictions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('country_id')->constrained('countries')->cascadeOnDelete();
            $table->foreignUuid('target_country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->foreignUuid('commodity_id')->nullable()->constrained('commodities')->nullOnDelete();
            $table->string('restriction_type'); // Import Ban, Export Ban, Tariff, Embargo
            $table->text('description');
            $table->decimal('tariff_percentage', 5, 2)->default(0);
            $table->date('effective_date');
            $table->date('end_date')->nullable();
            $table->timestamps();
        });

        // Port Risks
        Schema::create('port_risks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('port_id')->constrained('ports')->cascadeOnDelete();
            $table->string('status'); // Operational, Congested, Closed, Strike
            $table->integer('congestion_level')->default(0); // 0-100
            $table->integer('average_waiting_time')->default(0); // Hours
            $table->integer('security_level')->default(0);
            $table->integer('weather_impact')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('port_risks');
        Schema::dropIfExists('trade_restrictions');
        Schema::dropIfExists('global_risks');
        Schema::dropIfExists('risk_events');
        Schema::dropIfExists('weather_alerts');
        Schema::dropIfExists('weather_reports');
        Schema::dropIfExists('currency_forecasts');
        Schema::dropIfExists('currency_rates');
    }
};
