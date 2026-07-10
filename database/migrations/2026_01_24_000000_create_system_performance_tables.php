<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // System Metrics (CPU, Memory, Disk)
        Schema::create('system_metrics', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('server_name')->default('app-server-01');
            $table->decimal('cpu_usage_percentage', 5, 2)->default(0);
            $table->decimal('memory_usage_percentage', 5, 2)->default(0);
            $table->decimal('disk_usage_percentage', 5, 2)->default(0);
            $table->timestamp('recorded_at');
            $table->timestamps();
        });

        // Performance Logs (Request latency, Concurrent users)
        Schema::create('performance_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('endpoint')->nullable();
            $table->decimal('response_time_ms', 10, 2)->default(0);
            $table->integer('concurrent_users')->default(0);
            $table->integer('queries_executed')->default(0);
            $table->timestamp('recorded_at');
            $table->timestamps();
        });

        // Queue Metrics (Laravel Horizon style tracking)
        Schema::create('queue_metrics', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('queue_name'); // shipment-processing, ai-prediction
            $table->integer('pending_jobs')->default(0);
            $table->integer('completed_jobs')->default(0);
            $table->integer('failed_jobs')->default(0);
            $table->decimal('average_wait_time_ms', 10, 2)->default(0);
            $table->timestamp('recorded_at');
            $table->timestamps();
        });

        // Cache Metrics (Redis Hits/Misses)
        Schema::create('cache_metrics', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('cache_store')->default('redis');
            $table->integer('keys_count')->default(0);
            $table->integer('hits')->default(0);
            $table->integer('misses')->default(0);
            $table->decimal('memory_used_mb', 10, 2)->default(0);
            $table->timestamp('recorded_at');
            $table->timestamps();
        });

        // API Performance (Internal system tracking of external API latency)
        Schema::create('api_performance', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('provider_name');
            $table->decimal('average_latency_ms', 10, 2)->default(0);
            $table->integer('total_requests')->default(0);
            $table->integer('error_rate_percentage')->default(0);
            $table->timestamp('recorded_at');
            $table->timestamps();
        });

        // Server Health (Aggregate Status)
        Schema::create('server_health', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('component'); // Database, Redis, Reverb, Horizon
            $table->string('status'); // Healthy, Degraded, Down
            $table->text('details')->nullable();
            $table->timestamp('last_checked_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('server_health');
        Schema::dropIfExists('api_performance');
        Schema::dropIfExists('cache_metrics');
        Schema::dropIfExists('queue_metrics');
        Schema::dropIfExists('performance_logs');
        Schema::dropIfExists('system_metrics');
    }
};
