<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Commodity Forecasts (Machine Learning Predictions)
        Schema::create('commodity_forecasts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('commodity_id')->constrained('commodities')->cascadeOnDelete();
            $table->string('forecast_period'); // Tomorrow, 7 Days, 30 Days, etc
            $table->decimal('predicted_price', 20, 2)->default(0);
            $table->decimal('predicted_demand_index', 8, 2)->default(0);
            $table->decimal('predicted_supply_index', 8, 2)->default(0);
            $table->decimal('predicted_profit_margin', 8, 2)->default(0);
            $table->integer('confidence_score')->default(0);
            $table->timestamps();
        });

        // Commodity Scores (AI Aggregate Scores)
        Schema::create('commodity_scores', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('commodity_id')->constrained('commodities')->cascadeOnDelete();
            $table->integer('demand_score')->default(0);
            $table->integer('supply_score')->default(0);
            $table->integer('risk_score')->default(0);
            $table->integer('profit_score')->default(0);
            $table->integer('shipping_score')->default(0);
            $table->integer('weather_score')->default(0);
            $table->integer('currency_score')->default(0);
            $table->integer('overall_score')->default(0);
            $table->timestamps();
        });

        // Commodity Country Prices (Localization of Price & Tax)
        Schema::create('commodity_country_prices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('commodity_id')->constrained('commodities')->cascadeOnDelete();
            $table->foreignUuid('country_id')->constrained('countries')->cascadeOnDelete();
            $table->decimal('current_price', 20, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->decimal('import_tax_percentage', 5, 2)->default(0);
            $table->decimal('export_tax_percentage', 5, 2)->default(0);
            $table->decimal('average_shipping_cost', 15, 2)->default(0);
            $table->decimal('average_profit_margin', 5, 2)->default(0);
            $table->timestamps();
        });
        
        // Extend commodities table for UI attributes
        Schema::table('commodities', function (Blueprint $table) {
            $table->decimal('market_capitalization', 20, 2)->default(0)->after('description');
            $table->decimal('global_production', 20, 2)->default(0)->after('market_capitalization');
            $table->decimal('global_consumption', 20, 2)->default(0)->after('global_production');
            $table->decimal('global_stock', 20, 2)->default(0)->after('global_consumption');
            $table->string('volatility_index')->default('Low')->after('global_stock'); // Low, Medium, High
        });
    }

    public function down(): void
    {
        Schema::table('commodities', function (Blueprint $table) {
            $table->dropColumn([
                'market_capitalization', 'global_production', 
                'global_consumption', 'global_stock', 'volatility_index'
            ]);
        });

        Schema::dropIfExists('commodity_country_prices');
        Schema::dropIfExists('commodity_scores');
        Schema::dropIfExists('commodity_forecasts');
    }
};
