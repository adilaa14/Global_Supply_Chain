<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Analytics Dashboards (Customizable Executive Layouts)
        Schema::create('analytics_dashboards', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('dashboard_name');
            $table->text('layout_config'); // JSON of grid layout
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // Analytics KPIs (Dynamic KPI Tracker)
        Schema::create('analytics_kpis', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kpi_name');
            $table->string('category'); // Financial, Operational, Trade
            $table->decimal('current_value', 20, 2)->default(0);
            $table->decimal('previous_value', 20, 2)->default(0);
            $table->decimal('target_value', 20, 2)->default(0);
            $table->string('trend')->default('Stable'); // Up, Down, Stable
            $table->timestamp('calculated_at');
            $table->timestamps();
        });

        // Analytics Charts (Chart Definitions and Data)
        Schema::create('analytics_charts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('dashboard_id')->constrained('analytics_dashboards')->cascadeOnDelete();
            $table->string('chart_title');
            $table->string('chart_type'); // Bar, Line, Radar, Pie
            $table->string('data_source'); // API Endpoint or Query Model
            $table->text('chart_config')->nullable(); // JSON style configs
            $table->integer('position_order')->default(0);
            $table->timestamps();
        });

        // Report Templates (Custom Builder)
        Schema::create('report_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('template_name');
            $table->text('selected_modules'); // JSON (e.g. Financial, Shipping, Commodities)
            $table->text('selected_kpis'); // JSON
            $table->string('export_format')->default('PDF'); // PDF, Excel, PowerPoint
            $table->timestamps();
        });

        // Scheduled Reports (Automated Email Distributions)
        Schema::create('scheduled_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('template_id')->constrained('report_templates')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('frequency'); // Daily, Weekly, Monthly
            $table->time('send_time');
            $table->text('recipient_emails'); // JSON array
            $table->timestamp('last_sent_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Analytics Reports (Generated Logs/History)
        Schema::create('analytics_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('report_type'); // Executive Summary, Financial Report
            $table->foreignUuid('template_id')->nullable()->constrained('report_templates')->nullOnDelete();
            $table->foreignUuid('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('file_path');
            $table->string('file_format');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_reports');
        Schema::dropIfExists('scheduled_reports');
        Schema::dropIfExists('report_templates');
        Schema::dropIfExists('analytics_charts');
        Schema::dropIfExists('analytics_kpis');
        Schema::dropIfExists('analytics_dashboards');
    }
};
