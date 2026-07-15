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
        Schema::disableForeignKeyConstraints();
        
        Schema::dropIfExists('trade_opportunities');
        
        Schema::create('trade_opportunities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('country_id');
            $table->uuid('commodity_id');
            $table->decimal('opportunity_score', 8, 2)->default(0);
            $table->decimal('estimated_profit', 20, 2)->nullable();
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->foreign('country_id')->references('id')->on('countries')->cascadeOnDelete();
            $table->foreign('commodity_id')->references('id')->on('commodities')->cascadeOnDelete();
        });

        Schema::create('trade_simulations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable();
            $table->uuid('commodity_id');
            $table->uuid('origin_country_id');
            $table->uuid('destination_country_id');
            $table->decimal('quantity', 15, 2)->default(1);
            $table->string('container_type')->nullable();
            $table->decimal('shipping_cost', 20, 2)->default(0);
            $table->decimal('insurance', 20, 2)->default(0);
            $table->decimal('import_tax', 20, 2)->default(0);
            $table->decimal('export_tax', 20, 2)->default(0);
            $table->string('currency')->default('USD');
            
            $table->decimal('revenue', 20, 2)->default(0);
            $table->decimal('cost', 20, 2)->default(0);
            $table->decimal('profit', 20, 2)->default(0);
            $table->decimal('margin', 8, 2)->default(0); // percentage
            $table->decimal('roi', 8, 2)->default(0); // percentage
            $table->decimal('break_even_point', 20, 2)->default(0);

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('commodity_id')->references('id')->on('commodities')->cascadeOnDelete();
            $table->foreign('origin_country_id')->references('id')->on('countries')->cascadeOnDelete();
            $table->foreign('destination_country_id')->references('id')->on('countries')->cascadeOnDelete();
        });

        Schema::create('trade_forecasts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('country_id')->nullable();
            $table->uuid('commodity_id')->nullable();
            $table->date('forecast_date');
            
            $table->decimal('predicted_demand', 20, 2)->nullable();
            $table->decimal('predicted_supply', 20, 2)->nullable();
            $table->decimal('predicted_price', 20, 2)->nullable();
            $table->decimal('predicted_profit', 20, 2)->nullable();
            
            $table->string('market_trend')->nullable(); // Up, Down, Stable
            $table->decimal('country_opportunity_score', 8, 2)->nullable();
            
            $table->timestamps();
        });

        Schema::create('trade_insights', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type'); // Best Country, Best Commodity, Highest Growth, Biggest Risk, Trade Recommendation, Market Summary
            $table->string('title');
            $table->text('description');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('trade_market_analysis', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('country_id');
            $table->uuid('commodity_id');
            $table->decimal('demand_score', 8, 2)->default(0);
            $table->decimal('supply_score', 8, 2)->default(0);
            $table->decimal('margin_score', 8, 2)->default(0);
            $table->decimal('growth_score', 8, 2)->default(0);
            $table->boolean('is_emerging_market')->default(false);
            $table->timestamps();

            $table->foreign('country_id')->references('id')->on('countries')->cascadeOnDelete();
            $table->foreign('commodity_id')->references('id')->on('commodities')->cascadeOnDelete();
        });

        Schema::create('trade_risk_analysis', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('country_id');
            $table->decimal('political_risk', 8, 2)->default(0);
            $table->decimal('currency_risk', 8, 2)->default(0);
            $table->decimal('economic_risk', 8, 2)->default(0);
            $table->decimal('weather_risk', 8, 2)->default(0);
            $table->decimal('shipping_risk', 8, 2)->default(0);
            $table->decimal('port_congestion', 8, 2)->default(0);
            $table->decimal('commodity_volatility', 8, 2)->default(0);
            $table->decimal('trade_restriction', 8, 2)->default(0);
            $table->decimal('total_risk_score', 8, 2)->default(0);
            $table->timestamps();

            $table->foreign('country_id')->references('id')->on('countries')->cascadeOnDelete();
        });

        Schema::create('alternative_destinations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('original_destination_id');
            $table->uuid('alternative_country_id');
            $table->uuid('commodity_id')->nullable();
            
            $table->text('reason')->nullable();
            
            // Comparisons compared to original
            $table->decimal('price_difference', 20, 2)->default(0);
            $table->decimal('demand_difference', 20, 2)->default(0);
            $table->decimal('shipping_cost_difference', 20, 2)->default(0);
            $table->decimal('risk_difference', 8, 2)->default(0);
            $table->integer('eta_difference_days')->default(0);
            $table->decimal('profit_difference', 20, 2)->default(0);
            
            $table->timestamps();

            $table->foreign('original_destination_id')->references('id')->on('countries')->cascadeOnDelete();
            $table->foreign('alternative_country_id')->references('id')->on('countries')->cascadeOnDelete();
            $table->foreign('commodity_id')->references('id')->on('commodities')->cascadeOnDelete();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('alternative_destinations');
        Schema::dropIfExists('trade_risk_analysis');
        Schema::dropIfExists('trade_market_analysis');
        Schema::dropIfExists('trade_insights');
        Schema::dropIfExists('trade_forecasts');
        Schema::dropIfExists('trade_simulations');
        Schema::dropIfExists('trade_opportunities');
        Schema::enableForeignKeyConstraints();
    }
};
