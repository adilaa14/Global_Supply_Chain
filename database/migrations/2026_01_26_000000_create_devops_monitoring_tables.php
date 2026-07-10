<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Deployments (CI/CD Deployments)
        Schema::create('deployments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('release_version_id')->constrained('release_versions')->cascadeOnDelete();
            $table->string('environment'); // Staging, Production
            $table->string('deployment_strategy'); // Rolling, Blue/Green, Canary
            $table->string('status'); // Pending, Deploying, Success, Failed, Rolled Back
            $table->foreignUuid('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // Infrastructure Status (Kubernetes/Docker specific node statuses)
        Schema::create('infrastructure_status', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('node_name'); // k8s-worker-1, db-cluster-primary
            $table->string('node_role'); // App Server, AI Container, Redis Node
            $table->string('status'); // Running, Terminated, Pending
            $table->decimal('cpu_load', 5, 2)->default(0);
            $table->decimal('memory_load', 5, 2)->default(0);
            $table->timestamp('last_heartbeat');
            $table->timestamps();
        });

        // System Alerts (Hardware/DevOps level alerts, e.g., Disk Full)
        Schema::create('system_alerts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('component'); // Database, Storage, API
            $table->string('alert_name'); // High CPU, Out of Memory
            $table->string('severity'); // Warning, Critical
            $table->text('details');
            $table->boolean('is_resolved')->default(false);
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });

        // Incident Logs (System Outages and Downtime tracking)
        Schema::create('incident_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('description');
            $table->string('status'); // Investigating, Identified, Monitoring, Resolved
            $table->timestamp('started_at');
            $table->timestamp('resolved_at')->nullable();
            $table->text('root_cause_analysis')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incident_logs');
        Schema::dropIfExists('system_alerts');
        Schema::dropIfExists('infrastructure_status');
        Schema::dropIfExists('deployments');
    }
};
