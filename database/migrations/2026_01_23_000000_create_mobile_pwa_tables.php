<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Device Sessions (Tracking Mobile/Desktop hardware specifically for PWA push)
        Schema::create('device_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('device_id')->unique(); // Hardware or generated UUID
            $table->string('device_type'); // Mobile, Tablet, Desktop
            $table->string('os')->nullable(); // iOS, Android, Windows
            $table->string('browser')->nullable();
            $table->boolean('is_pwa_installed')->default(false);
            $table->timestamp('last_active_at')->nullable();
            $table->timestamps();
        });

        // Push Subscriptions (Web Push / Firebase Cloud Messaging)
        Schema::create('push_subscriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('device_session_id')->constrained('device_sessions')->cascadeOnDelete();
            $table->text('endpoint'); // Push endpoint URL
            $table->string('p256dh_key')->nullable();
            $table->string('auth_token')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Offline Sync Logs (Tracking failed mutations while offline)
        Schema::create('offline_sync_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('device_session_id')->constrained('device_sessions')->cascadeOnDelete();
            $table->string('mutation_type'); // Create, Update, Delete
            $table->string('entity_type'); // Shipment, Commodity
            $table->text('payload'); // JSON
            $table->string('status')->default('Pending'); // Pending, Synced, Failed, Conflict
            $table->text('conflict_resolution')->nullable();
            $table->timestamps();
        });

        // Mobile Preferences
        Schema::create('mobile_preferences', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('offline_mode_enabled')->default(true);
            $table->integer('max_cache_size_mb')->default(50);
            $table->boolean('sync_on_wifi_only')->default(false);
            $table->string('default_bottom_tab')->default('Dashboard');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mobile_preferences');
        Schema::dropIfExists('offline_sync_logs');
        Schema::dropIfExists('push_subscriptions');
        Schema::dropIfExists('device_sessions');
    }
};
