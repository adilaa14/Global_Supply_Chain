<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Comparison Results (Stores the snapshot of data at the time of comparison)
        Schema::create('comparison_results', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('comparison_id')->constrained('country_comparisons')->cascadeOnDelete();
            $table->foreignUuid('country_id')->constrained('countries');
            $table->text('metrics_snapshot'); // JSON of the 50+ indicators
            $table->integer('ai_overall_score')->default(0);
            $table->text('ai_reasoning')->nullable(); // JSON containing confidence score, reason, supporting indicators
            $table->timestamps();
        });

        // Comparison Histories
        Schema::create('comparison_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('comparison_id')->constrained('country_comparisons')->cascadeOnDelete();
            $table->string('action'); // Created, Updated, Exported, Shared
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Extend country_comparisons
        Schema::table('country_comparisons', function (Blueprint $table) {
            $table->foreignUuid('commodity_id')->nullable()->constrained('commodities')->nullOnDelete()->after('comparison_name');
            $table->text('ai_final_recommendation')->nullable()->after('commodity_id'); // Best Country, Lowest Risk, etc
        });
    }

    public function down(): void
    {
        Schema::table('country_comparisons', function (Blueprint $table) {
            $table->dropForeign(['commodity_id']);
            $table->dropColumn(['commodity_id', 'ai_final_recommendation']);
        });

        Schema::dropIfExists('comparison_histories');
        Schema::dropIfExists('comparison_results');
    }
};
