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
        
        // Drop tables if exist (for dev)
        Schema::dropIfExists('commodity_country_prices');
        Schema::dropIfExists('commodity_forecasts');
        Schema::dropIfExists('commodity_markets');
        Schema::dropIfExists('commodity_rankings');
        Schema::dropIfExists('commodity_supplies');
        Schema::dropIfExists('commodity_demands');
        Schema::dropIfExists('commodity_price_histories');
        Schema::dropIfExists('commodity_prices');

        Schema::enableForeignKeyConstraints();

        // commodities and commodity_categories already exist from enterprise_schema.

        Schema::create('commodity_prices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('commodity_id');
            $table->decimal('current_price', 15, 2);
            $table->decimal('open_price', 15, 2)->nullable();
            $table->decimal('close_price', 15, 2)->nullable();
            $table->decimal('high', 15, 2)->nullable();
            $table->decimal('low', 15, 2)->nullable();
            $table->decimal('average', 15, 2)->nullable();
            $table->decimal('moving_average', 15, 2)->nullable();
            $table->decimal('price_change', 10, 4)->nullable(); // % change
            $table->decimal('daily_change', 10, 4)->nullable();
            $table->decimal('weekly_change', 10, 4)->nullable();
            $table->decimal('monthly_change', 10, 4)->nullable();
            $table->decimal('yearly_change', 10, 4)->nullable();
            $table->decimal('volatility', 10, 4)->nullable();
            $table->string('trend')->nullable(); // Up, Down, Stable
            $table->timestamp('last_calculated_at')->nullable();
            $table->timestamps();

            $table->foreign('commodity_id')->references('id')->on('commodities')->onDelete('cascade');
        });

        Schema::create('commodity_price_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('commodity_id');
            $table->date('date');
            $table->decimal('price', 15, 2);
            $table->timestamps();

            $table->foreign('commodity_id')->references('id')->on('commodities')->onDelete('cascade');
            $table->unique(['commodity_id', 'date']);
        });

        Schema::create('commodity_demands', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('commodity_id');
            $table->decimal('current_demand', 15, 2)->nullable();
            $table->decimal('demand_score', 5, 2)->nullable(); // e.g. 0-100
            $table->decimal('demand_growth', 10, 4)->nullable(); // %
            $table->json('top_buyers')->nullable();
            $table->json('emerging_markets')->nullable();
            $table->string('consumption_trend')->nullable();
            $table->year('year');
            $table->timestamps();

            $table->foreign('commodity_id')->references('id')->on('commodities')->onDelete('cascade');
        });

        Schema::create('commodity_supplies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('commodity_id');
            $table->decimal('current_supply', 15, 2)->nullable();
            $table->decimal('supply_score', 5, 2)->nullable();
            $table->decimal('supply_growth', 10, 4)->nullable(); // %
            $table->decimal('production_volume', 15, 2)->nullable();
            $table->decimal('stock_level', 15, 2)->nullable();
            $table->json('major_producers')->nullable();
            $table->year('year');
            $table->timestamps();

            $table->foreign('commodity_id')->references('id')->on('commodities')->onDelete('cascade');
        });

        Schema::create('commodity_rankings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('commodity_id');
            $table->integer('global_ranking')->nullable();
            $table->integer('demand_ranking')->nullable();
            $table->integer('supply_ranking')->nullable();
            $table->timestamps();

            $table->foreign('commodity_id')->references('id')->on('commodities')->onDelete('cascade');
        });

        Schema::create('commodity_markets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('commodity_id');
            $table->decimal('global_demand', 15, 2)->nullable();
            $table->decimal('global_supply', 15, 2)->nullable();
            $table->json('top_exporting_countries')->nullable();
            $table->json('top_importing_countries')->nullable();
            $table->json('major_producers')->nullable();
            $table->json('major_consumers')->nullable();
            $table->decimal('market_share', 5, 2)->nullable();
            $table->string('price_trend')->nullable();
            $table->timestamps();

            $table->foreign('commodity_id')->references('id')->on('commodities')->onDelete('cascade');
        });

        Schema::create('commodity_forecasts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('commodity_id');
            $table->date('forecast_date');
            $table->decimal('predicted_price', 15, 2);
            $table->decimal('predicted_demand', 15, 2)->nullable();
            $table->decimal('predicted_supply', 15, 2)->nullable();
            $table->string('confidence_level')->nullable(); // High, Medium, Low
            $table->timestamps();

            $table->foreign('commodity_id')->references('id')->on('commodities')->onDelete('cascade');
        });

        Schema::create('commodity_country_prices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('commodity_id');
            $table->uuid('country_id'); // From countries table
            $table->decimal('selling_price', 15, 2)->nullable();
            $table->decimal('buying_price', 15, 2)->nullable();
            $table->decimal('import_cost', 15, 2)->nullable();
            $table->decimal('export_cost', 15, 2)->nullable();
            $table->decimal('shipping_cost', 15, 2)->nullable();
            $table->decimal('estimated_profit', 15, 2)->nullable();
            $table->timestamps();

            $table->foreign('commodity_id')->references('id')->on('commodities')->onDelete('cascade');
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
            $table->unique(['commodity_id', 'country_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commodity_country_prices');
        Schema::dropIfExists('commodity_forecasts');
        Schema::dropIfExists('commodity_markets');
        Schema::dropIfExists('commodity_rankings');
        Schema::dropIfExists('commodity_supplies');
        Schema::dropIfExists('commodity_demands');
        Schema::dropIfExists('commodity_price_histories');
        Schema::dropIfExists('commodity_prices');
    }
};
