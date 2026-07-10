<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // dashboard_preferences
        Schema::create('dashboard_preferences', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('layout_id')->default('default');
            $table->json('visible_widgets')->nullable();
            $table->json('favorite_countries')->nullable();
            $table->json('favorite_commodities')->nullable();
            $table->json('favorite_routes')->nullable();
            $table->json('default_filters')->nullable();
            $table->timestamps();
        });

        // dashboard_widgets
        Schema::create('dashboard_widgets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('widget_key');
            $table->integer('order')->default(0);
            $table->boolean('is_enabled')->default(true);
            $table->json('settings')->nullable();
            $table->timestamps();
        });

        // dashboard_snapshots
        Schema::create('dashboard_snapshots', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('company_id')->nullable()->constrained('companies')->cascadeOnDelete(); // Null for global admin snapshot
            $table->date('snapshot_date');
            $table->decimal('total_revenue', 15, 2)->default(0);
            $table->decimal('net_profit', 15, 2)->default(0);
            $table->integer('total_shipments')->default(0);
            $table->json('country_performance')->nullable();
            $table->json('commodity_performance')->nullable();
            $table->timestamps();
        });

        // activity_timelines
        Schema::create('activity_timelines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->string('type'); // Shipment Created, Alert, etc.
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('meta_data')->nullable(); // Related IDs, Old/New states
            $table->timestamps();
        });

        // global_alerts
        Schema::create('global_alerts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('category'); // Weather Alert, Risk Alert, Port Congestion
            $table->string('severity'); // Low, Medium, High, Critical
            $table->string('title');
            $table->text('message');
            $table->string('location')->nullable(); // e.g., CNSHG
            $table->decimal('lat', 10, 6)->nullable();
            $table->decimal('lng', 10, 6)->nullable();
            $table->integer('impact_score')->default(0);
            $table->json('affected_entities')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // dashboard_metrics
        Schema::create('dashboard_metrics', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->string('metric_key')->index(); // e.g., 'active_shipments', 'monthly_revenue'
            $table->decimal('numeric_value', 20, 2)->nullable();
            $table->string('string_value')->nullable();
            $table->json('json_value')->nullable();
            $table->timestamp('calculated_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dashboard_metrics');
        Schema::dropIfExists('global_alerts');
        Schema::dropIfExists('activity_timelines');
        Schema::dropIfExists('dashboard_snapshots');
        Schema::dropIfExists('dashboard_widgets');
        Schema::dropIfExists('dashboard_preferences');
    }
};
