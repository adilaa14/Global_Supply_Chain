<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Release Versions
        Schema::create('release_versions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('version_number')->unique(); // e.g., 1.0.0
            $table->string('release_name')->nullable();
            $table->text('release_notes')->nullable();
            $table->string('status'); // Planning, Development, Testing, Staging, Released
            $table->date('target_release_date')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->timestamps();
        });

        // Test Runs (Group of tests executed in CI/CD)
        Schema::create('test_runs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('release_version_id')->nullable()->constrained('release_versions')->nullOnDelete();
            $table->string('environment'); // Local, CI/CD, Staging, Production
            $table->string('triggered_by')->nullable(); // User or CI Pipeline
            $table->string('status'); // Running, Passed, Failed
            $table->integer('total_tests')->default(0);
            $table->integer('passed_tests')->default(0);
            $table->integer('failed_tests')->default(0);
            $table->decimal('execution_time_ms', 10, 2)->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // Test Results (Individual test assertions)
        Schema::create('test_results', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('test_run_id')->constrained('test_runs')->cascadeOnDelete();
            $table->string('test_suite'); // Unit, Feature, API, Browser
            $table->string('test_name');
            $table->string('status'); // Passed, Failed, Skipped
            $table->text('error_message')->nullable();
            $table->decimal('duration_ms', 8, 2)->default(0);
            $table->timestamps();
        });

        // Quality Metrics (Code coverage, SonarQube metrics)
        Schema::create('quality_metrics', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('release_version_id')->constrained('release_versions')->cascadeOnDelete();
            $table->decimal('unit_test_coverage', 5, 2)->default(0);
            $table->decimal('feature_test_coverage', 5, 2)->default(0);
            $table->decimal('api_coverage', 5, 2)->default(0);
            $table->integer('performance_score')->default(0);
            $table->integer('security_score')->default(0);
            $table->timestamp('evaluated_at');
            $table->timestamps();
        });

        // Bug Reports (Internal Issue Tracker)
        Schema::create('bug_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('description');
            $table->string('severity'); // Low, Medium, High, Critical
            $table->string('status')->default('Open'); // Open, In Progress, Resolved, Closed
            $table->string('module')->nullable();
            $table->foreignUuid('reported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('release_version_id')->nullable()->constrained('release_versions')->nullOnDelete(); // Version where bug was found
            $table->text('resolution_notes')->nullable();
            $table->timestamps();
        });

        // Deployment Logs (Tracks physical server deployments)
        Schema::create('deployment_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('release_version_id')->constrained('release_versions')->cascadeOnDelete();
            $table->string('environment'); // Staging, Production
            $table->string('deployed_by'); // CI/CD System or User
            $table->string('status'); // In Progress, Success, Failed, Rolled Back
            $table->text('deployment_output')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deployment_logs');
        Schema::dropIfExists('bug_reports');
        Schema::dropIfExists('quality_metrics');
        Schema::dropIfExists('test_results');
        Schema::dropIfExists('test_runs');
        Schema::dropIfExists('release_versions');
    }
};
