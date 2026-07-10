<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Country Trade Statistics
        Schema::create('country_trade_statistics', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('country_id')->constrained('countries')->cascadeOnDelete();
            $table->decimal('trade_balance', 20, 2)->default(0);
            $table->decimal('import_volume', 20, 2)->default(0);
            $table->decimal('export_volume', 20, 2)->default(0);
            $table->decimal('import_growth', 5, 2)->default(0);
            $table->decimal('export_growth', 5, 2)->default(0);
            $table->decimal('trade_volume', 20, 2)->default(0);
            $table->text('top_imports')->nullable(); // JSON
            $table->text('top_exports')->nullable(); // JSON
            $table->text('main_partners')->nullable(); // JSON
            $table->text('trade_restrictions')->nullable();
            $table->text('trade_agreements')->nullable();
            $table->timestamps();
        });

        // Country Scores
        Schema::create('country_scores', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('country_id')->constrained('countries')->cascadeOnDelete();
            $table->integer('economic_score')->default(0);
            $table->integer('trade_score')->default(0);
            $table->integer('risk_score')->default(0);
            $table->integer('demand_score')->default(0);
            $table->integer('profit_score')->default(0);
            $table->integer('overall_score')->default(0);
            $table->string('health_status')->default('Stable'); // Excellent, Good, Stable, Warning, Critical
            $table->timestamps();
        });

        // Extend Countries table with required PRD 5A fields
        Schema::table('countries', function (Blueprint $table) {
            $table->string('language')->nullable()->after('currency_name');
            $table->text('main_industries')->nullable()->after('flag');
            $table->text('import_policy')->nullable()->after('main_industries');
            $table->text('export_policy')->nullable()->after('import_policy');
        });
    }

    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn(['language', 'main_industries', 'import_policy', 'export_policy']);
        });

        Schema::dropIfExists('country_scores');
        Schema::dropIfExists('country_trade_statistics');
    }
};
