<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // MODULE 1: ROLES (Managed by Spatie Permission)
        // MODULE 2: COMPANIES
        Schema::create('companies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('company_name');
            $table->string('company_type'); // Importer, Exporter, Both
            $table->string('business_license')->nullable();
            $table->string('tax_number')->nullable();
            $table->uuid('country_id')->nullable(); // Foreign key added later
            $table->string('city')->nullable();
            $table->text('address')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('logo')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        // MODULE 1 (cont): USERS
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('role_id')->nullable(); // Optional legacy compat
            $table->foreignUuid('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('password');
            $table->string('avatar')->nullable();
            $table->string('status')->default('active');
            $table->timestamp('last_login')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        // MODULE 3: COUNTRIES
        Schema::create('countries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('iso_code', 3)->unique();
            $table->string('country_name');
            $table->string('capital')->nullable();
            $table->string('region')->nullable();
            $table->string('sub_region')->nullable();
            $table->string('currency_code', 3)->nullable();
            $table->string('currency_name')->nullable();
            $table->bigInteger('population')->nullable();
            $table->string('timezone')->nullable();
            $table->decimal('latitude', 10, 6)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();
            $table->string('flag')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        // Add foreign key back to companies
        Schema::table('companies', function (Blueprint $table) {
            $table->foreign('country_id')->references('id')->on('countries')->nullOnDelete();
        });

        // MODULE 4: PORTS
        Schema::create('ports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('country_id')->constrained('countries')->cascadeOnDelete();
            $table->string('port_code')->unique();
            $table->string('port_name');
            $table->string('city')->nullable();
            $table->decimal('latitude', 10, 6)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();
            $table->decimal('capacity', 15, 2)->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        // MODULE 5: SHIPS
        Schema::create('ships', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('imo_number')->unique();
            $table->string('mmsi')->nullable();
            $table->string('ship_name');
            $table->string('ship_type')->nullable();
            $table->decimal('capacity', 15, 2)->nullable();
            $table->decimal('current_latitude', 10, 6)->nullable();
            $table->decimal('current_longitude', 10, 6)->nullable();
            $table->decimal('speed', 8, 2)->nullable();
            $table->decimal('heading', 8, 2)->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        // MODULE 6: COMMODITIES
        Schema::create('commodity_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('commodities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('category_id')->constrained('commodity_categories')->cascadeOnDelete();
            $table->string('commodity_code')->unique();
            $table->string('commodity_name');
            $table->string('unit')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        // MODULE 7: COMMODITY PRICES
        Schema::create('commodity_prices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('commodity_id')->constrained('commodities')->cascadeOnDelete();
            $table->foreignUuid('country_id')->constrained('countries')->cascadeOnDelete();
            $table->decimal('price', 15, 2);
            $table->string('currency', 3);
            $table->date('price_date');
            $table->string('source')->nullable();
            $table->string('trend')->nullable();
            $table->string('forecast')->nullable();
            $table->timestamps();
        });

        // MODULE 8: SHIPMENTS
        Schema::create('shipments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('shipment_number')->unique();
            $table->string('shipment_type'); // Import, Export
            $table->foreignUuid('commodity_id')->constrained('commodities');
            $table->foreignUuid('ship_id')->nullable()->constrained('ships');
            $table->foreignUuid('origin_country_id')->constrained('countries');
            $table->foreignUuid('destination_country_id')->constrained('countries');
            $table->foreignUuid('origin_port_id')->constrained('ports');
            $table->foreignUuid('destination_port_id')->constrained('ports');
            $table->decimal('quantity', 15, 2);
            $table->string('unit')->nullable();
            $table->decimal('weight', 15, 2)->nullable();
            $table->integer('container_count')->nullable();
            $table->decimal('estimated_value', 20, 2)->nullable();
            $table->timestamp('departure_date')->nullable();
            $table->timestamp('estimated_arrival')->nullable();
            $table->timestamp('actual_arrival')->nullable();
            $table->string('status')->default('Draft');
            $table->integer('risk_score')->default(0);
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // MODULE 9: SHIPMENT POSITIONS
        Schema::create('shipment_positions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('shipment_id')->constrained('shipments')->cascadeOnDelete();
            $table->decimal('latitude', 10, 6);
            $table->decimal('longitude', 10, 6);
            $table->decimal('speed', 8, 2)->nullable();
            $table->decimal('heading', 8, 2)->nullable();
            $table->timestamp('recorded_at');
            $table->timestamps();
        });

        // MODULE 10: SHIPMENT REDIRECTS
        Schema::create('shipment_redirects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('shipment_id')->constrained('shipments')->cascadeOnDelete();
            $table->foreignUuid('old_country_id')->constrained('countries');
            $table->foreignUuid('new_country_id')->constrained('countries');
            $table->foreignUuid('old_port_id')->constrained('ports');
            $table->foreignUuid('new_port_id')->constrained('ports');
            $table->text('reason')->nullable();
            $table->decimal('additional_distance', 15, 2)->default(0);
            $table->decimal('additional_cost', 15, 2)->default(0);
            $table->decimal('estimated_profit', 20, 2)->nullable();
            $table->integer('recommendation_score')->default(0);
            $table->foreignUuid('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        // MODULE 11: COUNTRY ECONOMY
        Schema::create('country_economies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('country_id')->constrained('countries')->cascadeOnDelete();
            $table->decimal('gdp', 20, 2)->nullable();
            $table->decimal('inflation', 5, 2)->nullable();
            $table->decimal('interest_rate', 5, 2)->nullable();
            $table->decimal('unemployment', 5, 2)->nullable();
            $table->decimal('exchange_rate', 15, 6)->nullable();
            $table->timestamp('last_update')->nullable();
            $table->timestamps();
        });

        // MODULE 12: COUNTRY RISKS
        Schema::create('country_risks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('country_id')->constrained('countries')->cascadeOnDelete();
            $table->integer('political_risk')->default(0);
            $table->integer('economic_risk')->default(0);
            $table->integer('weather_risk')->default(0);
            $table->integer('security_risk')->default(0);
            $table->integer('trade_risk')->default(0);
            $table->integer('overall_risk')->default(0);
            $table->timestamps();
        });

        // MODULE 13: COUNTRY DEMAND
        Schema::create('country_demands', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('country_id')->constrained('countries')->cascadeOnDelete();
            $table->foreignUuid('commodity_id')->constrained('commodities')->cascadeOnDelete();
            $table->integer('demand_score')->default(0);
            $table->decimal('import_volume', 20, 2)->default(0);
            $table->decimal('export_volume', 20, 2)->default(0);
            $table->string('market_growth')->nullable();
            $table->string('forecast')->nullable();
            $table->timestamps();
        });

        // MODULE 14: COUNTRY COMPARISON
        Schema::create('country_comparisons', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('comparison_name')->nullable();
            $table->timestamps();
        });

        // MODULE 15: TRADE OPPORTUNITIES
        Schema::create('trade_opportunities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('commodity_id')->constrained('commodities')->cascadeOnDelete();
            $table->foreignUuid('country_id')->constrained('countries')->cascadeOnDelete();
            $table->integer('opportunity_score')->default(0);
            $table->decimal('expected_profit', 20, 2)->nullable();
            $table->integer('risk_score')->default(0);
            $table->text('recommendation')->nullable();
            $table->timestamps();
        });

        // MODULE 16: PROFIT SIMULATIONS
        Schema::create('profit_simulations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('shipment_id')->constrained('shipments')->cascadeOnDelete();
            $table->foreignUuid('current_country_id')->constrained('countries');
            $table->foreignUuid('recommended_country_id')->constrained('countries');
            $table->decimal('current_profit', 20, 2)->default(0);
            $table->decimal('estimated_profit', 20, 2)->default(0);
            $table->decimal('additional_cost', 15, 2)->default(0);
            $table->decimal('fuel_cost', 15, 2)->default(0);
            $table->decimal('insurance_cost', 15, 2)->default(0);
            $table->decimal('tax_cost', 15, 2)->default(0);
            $table->integer('eta_difference')->default(0); // hours
            $table->integer('recommendation_score')->default(0);
            $table->timestamps();
        });

        // MODULE 17: WEATHER
        Schema::create('weather', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('country_id')->constrained('countries')->cascadeOnDelete();
            $table->decimal('temperature', 5, 2)->nullable();
            $table->decimal('humidity', 5, 2)->nullable();
            $table->decimal('wind_speed', 8, 2)->nullable();
            $table->decimal('wave_height', 8, 2)->nullable();
            $table->decimal('visibility', 8, 2)->nullable();
            $table->string('condition')->nullable();
            $table->integer('weather_risk')->default(0);
            $table->timestamp('recorded_at')->nullable();
            $table->timestamps();
        });

        // MODULE 18: CURRENCY
        Schema::create('currencies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('currency_code', 3)->unique();
            $table->string('currency_name');
            $table->decimal('exchange_rate', 15, 6)->nullable();
            $table->decimal('daily_change', 5, 2)->default(0);
            $table->decimal('weekly_change', 5, 2)->default(0);
            $table->decimal('monthly_change', 5, 2)->default(0);
            $table->timestamps();
        });

        // MODULE 19: NEWS
        Schema::create('news', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->string('title');
            $table->string('category')->nullable();
            $table->text('summary')->nullable();
            $table->string('sentiment')->nullable(); // Positive, Neutral, Negative
            $table->timestamp('published_at')->nullable();
            $table->string('source')->nullable();
            $table->timestamps();
        });

        // MODULE 20: NOTIFICATIONS
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('message');
            $table->string('type')->nullable();
            $table->string('priority')->default('low'); // Critical, High, Medium, Low
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        // MODULE 21: REPORTS
        Schema::create('reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('report_type');
            $table->string('file_name');
            $table->foreignUuid('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();
        });

        // MODULE 22: AUDIT LOGS
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->string('module')->nullable();
            $table->text('description')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });

        // MODULE 23: SYSTEM SETTINGS
        Schema::create('settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // MODULE 24: API CACHE
        Schema::create('api_cache', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('provider');
            $table->string('endpoint');
            $table->string('response_hash')->unique();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_cache');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('reports');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('news');
        Schema::dropIfExists('currencies');
        Schema::dropIfExists('weather');
        Schema::dropIfExists('profit_simulations');
        Schema::dropIfExists('trade_opportunities');
        Schema::dropIfExists('country_comparisons');
        Schema::dropIfExists('country_demands');
        Schema::dropIfExists('country_risks');
        Schema::dropIfExists('country_economies');
        Schema::dropIfExists('shipment_redirects');
        Schema::dropIfExists('shipment_positions');
        Schema::dropIfExists('shipments');
        Schema::dropIfExists('commodity_prices');
        Schema::dropIfExists('commodities');
        Schema::dropIfExists('commodity_categories');
        Schema::dropIfExists('ships');
        Schema::dropIfExists('ports');
        Schema::table('companies', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
        });
        Schema::dropIfExists('countries');
        Schema::dropIfExists('users');
        Schema::dropIfExists('companies');
    }
};
