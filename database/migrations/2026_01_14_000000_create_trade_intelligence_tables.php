<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Trade Intelligence (Aggregated metrics for the command center)
        Schema::create('trade_intelligence', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->decimal('global_trade_volume', 20, 2)->default(0);
            $table->decimal('total_import_value', 20, 2)->default(0);
            $table->decimal('total_export_value', 20, 2)->default(0);
            $table->decimal('today_opportunity_score', 5, 2)->default(0);
            $table->timestamp('calculated_at');
            $table->timestamps();
        });

        // Trade Scores (Executive summary scoring)
        Schema::create('trade_scores', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('entity_type'); // Country, Commodity, Port
            $table->string('entity_id');
            $table->integer('trade_success_score')->default(0);
            $table->integer('profitability_score')->default(0);
            $table->integer('risk_resilience_score')->default(0);
            $table->timestamps();
        });

        // Market Rankings (Top 20 lists, Emerging Markets, etc)
        Schema::create('market_rankings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ranking_category'); // Top Export, Top Import, Highest ROI, Emerging
            $table->integer('rank_position');
            $table->foreignUuid('country_id')->constrained('countries')->cascadeOnDelete();
            $table->foreignUuid('commodity_id')->nullable()->constrained('commodities')->nullOnDelete();
            $table->decimal('score', 8, 2)->default(0);
            $table->text('reasoning')->nullable();
            $table->timestamps();
        });

        // Executive Summaries (AI generated daily text summaries)
        Schema::create('executive_summaries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->date('summary_date');
            $table->text('ai_generated_text');
            $table->text('recommended_actions'); // JSON
            $table->foreignUuid('top_country_export')->nullable()->constrained('countries')->nullOnDelete();
            $table->foreignUuid('top_country_import')->nullable()->constrained('countries')->nullOnDelete();
            $table->foreignUuid('highest_profit_commodity')->nullable()->constrained('commodities')->nullOnDelete();
            $table->foreignUuid('highest_risk_country')->nullable()->constrained('countries')->nullOnDelete();
            $table->timestamps();
        });

        // Trade Predictions (What-If analysis and business simulations)
        Schema::create('trade_predictions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('simulation_type'); // What-If (Fuel +15%), Commodity Price (+10%)
            $table->text('simulation_parameters'); // JSON
            $table->text('predicted_outcome'); // JSON
            $table->decimal('impact_on_profit', 20, 2)->default(0);
            $table->integer('impact_on_risk')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trade_predictions');
        Schema::dropIfExists('executive_summaries');
        Schema::dropIfExists('market_rankings');
        Schema::dropIfExists('trade_scores');
        Schema::dropIfExists('trade_intelligence');
    }
};
