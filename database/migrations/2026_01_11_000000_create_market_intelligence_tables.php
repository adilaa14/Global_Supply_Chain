<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Global Market Indices
        Schema::create('market_indices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('index_name'); // Global Trade Index, Global Risk Index, etc.
            $table->decimal('index_value', 8, 2)->default(0);
            $table->decimal('daily_change', 5, 2)->default(0);
            $table->string('trend')->nullable(); // Up, Down, Stable
            $table->timestamp('calculated_at');
            $table->timestamps();
        });

        // Market Opportunities (Aggregated from trade_opportunities but on a macro level)
        Schema::create('market_opportunities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('opportunity_type'); // Export, Import
            $table->foreignUuid('country_id')->constrained('countries')->cascadeOnDelete();
            $table->foreignUuid('commodity_id')->constrained('commodities')->cascadeOnDelete();
            $table->decimal('expected_price', 20, 2)->default(0);
            $table->decimal('expected_profit', 20, 2)->default(0);
            $table->string('market_growth')->nullable();
            $table->integer('risk_score')->default(0);
            $table->integer('ai_recommendation_score')->default(0);
            $table->timestamps();
        });

        // Commodity Markets (Macro data specific to commodities beyond just price)
        Schema::create('commodity_markets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('commodity_id')->constrained('commodities')->cascadeOnDelete();
            $table->decimal('global_demand_index', 8, 2)->default(0);
            $table->decimal('global_supply_index', 8, 2)->default(0);
            $table->decimal('change_24h', 5, 2)->default(0);
            $table->decimal('change_7d', 5, 2)->default(0);
            $table->decimal('change_30d', 5, 2)->default(0);
            $table->text('forecast_1d')->nullable();
            $table->text('forecast_7d')->nullable();
            $table->text('forecast_30d')->nullable();
            $table->text('forecast_90d')->nullable();
            $table->timestamps();
        });

        // Global Demands
        Schema::create('global_demands', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('commodity_id')->constrained('commodities')->cascadeOnDelete();
            $table->foreignUuid('country_id')->constrained('countries')->cascadeOnDelete(); // Highest Demand Country
            $table->decimal('demand_volume', 20, 2)->default(0);
            $table->decimal('growth_rate', 5, 2)->default(0);
            $table->string('demand_trend')->nullable();
            $table->timestamps();
        });

        // Global Supplies
        Schema::create('global_supplies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('commodity_id')->constrained('commodities')->cascadeOnDelete();
            $table->foreignUuid('country_id')->constrained('countries')->cascadeOnDelete(); // Producer Country
            $table->decimal('supply_volume', 20, 2)->default(0);
            $table->decimal('stock_availability', 20, 2)->default(0);
            $table->string('supply_trend')->nullable();
            $table->timestamps();
        });

        // Market Alerts (Macro level market shocks)
        Schema::create('market_alerts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('alert_type'); // Commodity Spike, Demand Surge, Trade Restriction
            $table->string('title');
            $table->text('description');
            $table->string('severity')->default('info');
            $table->foreignUuid('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->foreignUuid('commodity_id')->nullable()->constrained('commodities')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_alerts');
        Schema::dropIfExists('global_supplies');
        Schema::dropIfExists('global_demands');
        Schema::dropIfExists('commodity_markets');
        Schema::dropIfExists('market_opportunities');
        Schema::dropIfExists('market_indices');
    }
};
