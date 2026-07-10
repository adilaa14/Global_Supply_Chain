<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // AI Models Registry
        Schema::create('ai_models', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('model_name'); // Commodity Price Predictor, Risk Predictor
            $table->string('version')->default('1.0.0');
            $table->string('status')->default('Active'); // Active, Training, Deprecated
            $table->string('framework')->default('PyTorch'); // Scikit-Learn, TensorFlow, PyTorch
            $table->timestamp('last_trained_at')->nullable();
            $table->timestamps();
        });

        // Model Metrics (Accuracy, Precision, Recall tracking over time)
        Schema::create('model_metrics', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('model_id')->constrained('ai_models')->cascadeOnDelete();
            $table->decimal('accuracy', 5, 2)->default(0);
            $table->decimal('precision', 5, 2)->default(0);
            $table->decimal('recall', 5, 2)->default(0);
            $table->decimal('f1_score', 5, 2)->default(0);
            $table->decimal('prediction_drift', 5, 2)->default(0);
            $table->timestamp('evaluated_at');
            $table->timestamps();
        });

        // Training Logs
        Schema::create('ai_training_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('model_id')->constrained('ai_models')->cascadeOnDelete();
            $table->string('training_type'); // Manual, Scheduled, Incremental
            $table->integer('epochs_run')->default(0);
            $table->decimal('loss_value', 10, 6)->nullable();
            $table->string('status'); // Running, Completed, Failed
            $table->text('error_log')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // Feature Sets (Variables fed into the AI)
        Schema::create('feature_sets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('model_id')->constrained('ai_models')->cascadeOnDelete();
            $table->string('feature_name'); // Seasonality, Fuel Cost, GDP
            $table->decimal('importance_weight', 5, 4)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Prediction Results (Detailed Inference outputs)
        Schema::create('prediction_results', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('model_id')->constrained('ai_models')->cascadeOnDelete();
            $table->string('target_type'); // Commodity, Country, Shipment
            $table->string('target_id');
            $table->text('predicted_value'); // JSON or String
            $table->integer('confidence_score')->default(0);
            $table->decimal('inference_time_ms', 10, 2)->default(0);
            $table->text('reasoning')->nullable();
            $table->timestamps();
        });

        // Forecast Histories (To track if 180-day forecasts were eventually right)
        Schema::create('forecast_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('prediction_id')->constrained('prediction_results')->cascadeOnDelete();
            $table->date('target_date');
            $table->decimal('predicted_value', 20, 2)->default(0);
            $table->decimal('actual_value', 20, 2)->nullable(); // Filled when the day arrives
            $table->decimal('variance', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forecast_histories');
        Schema::dropIfExists('prediction_results');
        Schema::dropIfExists('feature_sets');
        Schema::dropIfExists('ai_training_logs');
        Schema::dropIfExists('model_metrics');
        Schema::dropIfExists('ai_models');
    }
};
