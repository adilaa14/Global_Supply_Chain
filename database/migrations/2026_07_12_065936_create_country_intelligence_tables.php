<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Alter existing countries table to add intelligence scores
        Schema::table('countries', function (Blueprint $table) {
            $table->decimal('risk_score', 5, 2)->nullable()->after('status');
            $table->decimal('opportunity_score', 5, 2)->nullable()->after('risk_score');
            $table->decimal('political_stability', 5, 2)->nullable()->after('opportunity_score');
            $table->string('trade_status')->nullable()->after('political_stability');
        });

        // Drop existing tables to redefine them with the correct sprint 4 schema
        Schema::dropIfExists('country_economies');
        Schema::dropIfExists('country_risks');
        Schema::dropIfExists('country_trade_statistics');

        Schema::create('country_economies', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('country_id')->constrained()->cascadeOnDelete();
            $table->decimal('gdp', 20, 2)->nullable(); // in USD
            $table->decimal('gdp_growth', 5, 2)->nullable(); // percentage
            $table->decimal('inflation_rate', 5, 2)->nullable();
            $table->decimal('interest_rate', 5, 2)->nullable();
            $table->decimal('exchange_rate', 15, 6)->nullable(); // to USD
            $table->decimal('unemployment_rate', 5, 2)->nullable();
            $table->decimal('consumer_price_index', 8, 2)->nullable();
            $table->decimal('producer_price_index', 8, 2)->nullable();
            $table->decimal('purchasing_power', 20, 2)->nullable();
            $table->integer('year')->nullable();
            $table->timestamps();
        });

        Schema::create('country_risks', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('country_id')->constrained()->cascadeOnDelete();
            $table->decimal('political_risk', 5, 2)->nullable();
            $table->decimal('economic_risk', 5, 2)->nullable();
            $table->decimal('natural_disaster_risk', 5, 2)->nullable();
            $table->decimal('war_risk', 5, 2)->nullable();
            $table->decimal('trade_restriction_risk', 5, 2)->nullable();
            $table->decimal('currency_risk', 5, 2)->nullable();
            $table->decimal('supply_chain_risk', 5, 2)->nullable();
            $table->decimal('overall_risk_score', 5, 2)->nullable();
            $table->timestamp('last_calculated_at')->nullable();
            $table->timestamps();
        });

        Schema::create('country_trade_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('country_id')->constrained()->cascadeOnDelete();
            $table->decimal('total_import', 20, 2)->nullable();
            $table->decimal('total_export', 20, 2)->nullable();
            $table->decimal('trade_balance', 20, 2)->nullable();
            $table->decimal('import_duty_avg', 5, 2)->nullable();
            $table->decimal('export_duty_avg', 5, 2)->nullable();
            $table->json('top_imported_commodities')->nullable();
            $table->json('top_exported_commodities')->nullable();
            $table->json('main_trade_partners')->nullable();
            $table->integer('year')->nullable();
            $table->timestamps();
        });

        Schema::create('country_regulations', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('country_id')->constrained()->cascadeOnDelete();
            $table->string('regulation_type'); // e.g., 'Import', 'Export', 'Tariff', 'Sanction'
            $table->text('description');
            $table->string('severity')->nullable(); // High, Medium, Low
            $table->date('effective_date')->nullable();
            $table->timestamps();
        });

        Schema::create('country_rankings', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('country_id')->constrained()->cascadeOnDelete();
            $table->decimal('logistics_performance_index', 5, 2)->nullable();
            $table->decimal('ease_of_doing_business', 5, 2)->nullable();
            $table->decimal('corruption_perception_index', 5, 2)->nullable();
            $table->decimal('human_development_index', 5, 2)->nullable();
            $table->integer('shipping_accessibility_rank')->nullable();
            $table->integer('year')->nullable();
            $table->timestamps();
        });

        Schema::create('country_opportunities', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('country_id')->constrained()->cascadeOnDelete();
            $table->decimal('demand_growth', 5, 2)->nullable();
            $table->decimal('import_opportunity_score', 5, 2)->nullable();
            $table->decimal('export_opportunity_score', 5, 2)->nullable();
            $table->decimal('market_size', 20, 2)->nullable();
            $table->decimal('market_growth', 5, 2)->nullable();
            $table->string('competitor_level')->nullable(); // High, Medium, Low
            $table->decimal('expected_profit_margin', 5, 2)->nullable();
            $table->json('recommended_commodities')->nullable();
            $table->decimal('overall_opportunity_score', 5, 2)->nullable();
            $table->timestamp('last_calculated_at')->nullable();
            $table->timestamps();
        });
        
        Schema::create('country_trade_agreements', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('country_id')->constrained()->cascadeOnDelete();
            $table->string('agreement_name');
            $table->string('partner_countries')->nullable();
            $table->string('status')->nullable();
            $table->date('signed_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('country_trade_agreements');
        Schema::dropIfExists('country_opportunities');
        Schema::dropIfExists('country_rankings');
        Schema::dropIfExists('country_regulations');
        Schema::dropIfExists('country_trade_statistics');
        Schema::dropIfExists('country_risks');
        Schema::dropIfExists('country_economies');
        
        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn(['risk_score', 'opportunity_score', 'political_stability', 'trade_status']);
        });
    }
};
