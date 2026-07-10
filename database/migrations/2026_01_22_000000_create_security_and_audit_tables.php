<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Security Incidents (Breaches, Malware, SQLi attempts)
        Schema::create('security_incidents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('incident_type'); // Brute Force, SQL Injection, Malware
            $table->string('severity'); // Low, Medium, High, Critical
            $table->text('description');
            $table->string('affected_module')->nullable();
            $table->string('status')->default('Open'); // Open, Investigating, Resolved
            $table->text('resolution_notes')->nullable();
            $table->string('source_ip', 45)->nullable();
            $table->foreignUuid('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });

        // User Sessions (Session Control Panel)
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('session_token')->unique();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('device')->nullable();
            $table->string('browser')->nullable();
            $table->string('location')->nullable();
            $table->timestamp('last_activity')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Failed Logins (Brute Force Tracking)
        Schema::create('failed_logins', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('email_attempted');
            $table->string('ip_address', 45);
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });

        // Security Alerts (Notifications sent to Admins)
        Schema::create('security_alerts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('alert_type'); // Multiple Failed Logins, Mass Deletion
            $table->text('message');
            $table->string('severity')->default('High');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        // Compliance Reports (GDPR, SOC2 generated docs)
        Schema::create('compliance_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('framework'); // GDPR, ISO 27001, SOC 2
            $table->string('report_name');
            $table->string('file_path');
            $table->foreignUuid('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Backup Logs (Verification of Cloud Backups)
        Schema::create('backup_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('backup_type'); // Full, Incremental
            $table->string('destination'); // S3, Azure Blob
            $table->string('file_name');
            $table->bigInteger('file_size_bytes')->default(0);
            $table->string('status'); // Success, Failed
            $table->text('error_message')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backup_logs');
        Schema::dropIfExists('compliance_reports');
        Schema::dropIfExists('security_alerts');
        Schema::dropIfExists('failed_logins');
        Schema::dropIfExists('user_sessions');
        Schema::dropIfExists('security_incidents');
    }
};
