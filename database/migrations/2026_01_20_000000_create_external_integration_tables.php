<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // API Providers (AIS, OpenWeather, Stripe, OpenAI, etc.)
        Schema::create('api_providers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->string('category'); // Weather, Currency, AIS, Payment
            $table->string('base_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('rate_limit_per_minute')->default(60);
            $table->string('auth_type')->default('Bearer'); // Bearer, Basic, API Key, OAuth2
            $table->timestamps();
        });

        // API Credentials (Encrypted storage for keys)
        Schema::create('api_credentials', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('provider_id')->constrained('api_providers')->cascadeOnDelete();
            $table->string('environment')->default('production'); // sandbox, production
            $table->text('api_key')->nullable();
            $table->text('api_secret')->nullable();
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        // API Logs (Audit trail for massive requests)
        Schema::create('api_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('provider_id')->constrained('api_providers')->cascadeOnDelete();
            $table->string('endpoint');
            $table->string('method', 10)->default('GET');
            $table->integer('status_code')->default(200);
            $table->decimal('response_time_ms', 10, 2)->default(0);
            $table->boolean('is_error')->default(false);
            $table->timestamps();
        });

        // Webhooks (Endpoints configured to receive external callbacks)
        Schema::create('webhooks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('source'); // AIS, Payment Gateway
            $table->string('endpoint_url');
            $table->text('secret_key')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Webhook Logs (Incoming payload history)
        Schema::create('webhook_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('webhook_id')->constrained('webhooks')->cascadeOnDelete();
            $table->text('payload_received'); // JSON
            $table->integer('processed_status')->default(200); // 200, 500
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        // Sync Jobs (Background scheduled synchronization)
        Schema::create('sync_jobs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('job_name'); // e.g. Sync Daily Commodity Prices
            $table->foreignUuid('provider_id')->constrained('api_providers')->cascadeOnDelete();
            $table->string('status')->default('Pending'); // Running, Completed, Failed
            $table->integer('records_processed')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('error_log')->nullable();
            $table->timestamps();
        });

        // Integration Health (Circuit Breaker Metrics)
        Schema::create('integration_health', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('provider_id')->constrained('api_providers')->cascadeOnDelete();
            $table->integer('success_rate_percentage')->default(100);
            $table->integer('error_count_last_hour')->default(0);
            $table->integer('average_latency_ms')->default(0);
            $table->string('status'); // Healthy, Degraded, Down
            $table->timestamp('last_checked_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('integration_health');
        Schema::dropIfExists('sync_jobs');
        Schema::dropIfExists('webhook_logs');
        Schema::dropIfExists('webhooks');
        Schema::dropIfExists('api_logs');
        Schema::dropIfExists('api_credentials');
        Schema::dropIfExists('api_providers');
    }
};
