<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // AI Recommendations
        Schema::create('ai_recommendations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('country_id')->constrained('countries')->cascadeOnDelete();
            $table->foreignUuid('commodity_id')->nullable()->constrained('commodities')->nullOnDelete();
            $table->string('recommendation_type'); // Export, Import, Redirect
            $table->integer('confidence_score')->default(0);
            $table->decimal('estimated_profit', 20, 2)->default(0);
            $table->integer('risk_score')->default(0);
            $table->integer('transit_time')->default(0); // hours
            $table->text('reasoning')->nullable(); // JSON
            $table->foreignUuid('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // AI Simulations (User Scenario Tests)
        Schema::create('ai_simulations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('scenario_parameters'); // JSON of input variables (quantity, target profit, etc)
            $table->text('current_plan_result'); // JSON 
            $table->text('alternative_plan_result'); // JSON
            $table->decimal('profit_difference', 20, 2)->default(0);
            $table->decimal('roi_difference', 5, 2)->default(0);
            $table->timestamps();
        });

        // AI Prediction Logs
        Schema::create('ai_prediction_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('prediction_type'); // Commodity Price, Country Risk, Demand
            $table->string('target_entity_id'); // UUID of commodity or country
            $table->text('predicted_value');
            $table->integer('confidence_score')->default(0);
            $table->text('supporting_indicators')->nullable();
            $table->timestamp('predicted_for_date');
            $table->timestamps();
        });

        // AI Learning History (Tracks if the AI was correct)
        Schema::create('ai_learning_history', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('recommendation_id')->constrained('ai_recommendations')->cascadeOnDelete();
            $table->string('status'); // Accepted, Rejected
            $table->decimal('predicted_profit', 20, 2)->default(0);
            $table->decimal('actual_profit', 20, 2)->nullable(); // Filled when shipment completes
            $table->decimal('accuracy_percentage', 5, 2)->nullable();
            $table->text('user_feedback')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_learning_history');
        Schema::dropIfExists('ai_prediction_logs');
        Schema::dropIfExists('ai_simulations');
        Schema::dropIfExists('ai_recommendations');
    }
};
