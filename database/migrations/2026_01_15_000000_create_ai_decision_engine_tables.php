<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // AI Decisions (Finalized decision records)
        Schema::create('ai_decisions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('shipment_id')->constrained('shipments')->cascadeOnDelete();
            $table->string('decision_type'); // Proceed, Redirect, Delay, Wait, Hold, Cancel
            $table->text('reasoning'); // Explainable AI text
            $table->integer('confidence_score')->default(0);
            $table->text('supporting_data')->nullable(); // JSON
            $table->decimal('expected_profit', 20, 2)->default(0);
            $table->decimal('expected_roi', 5, 2)->default(0);
            $table->string('status')->default('Pending'); // Pending, Approved, Rejected, Executed
            $table->timestamps();
        });

        // Decision Histories
        Schema::create('decision_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('decision_id')->constrained('ai_decisions')->cascadeOnDelete();
            $table->string('action'); // Approved, Rejected, Executed
            $table->text('notes')->nullable();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Redirect Requests (Approval Workflow)
        Schema::create('redirect_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('shipment_id')->constrained('shipments')->cascadeOnDelete();
            $table->foreignUuid('new_destination_country_id')->constrained('countries');
            $table->foreignUuid('new_destination_port_id')->constrained('ports');
            $table->decimal('additional_cost', 20, 2)->default(0);
            $table->decimal('expected_new_revenue', 20, 2)->default(0);
            $table->decimal('expected_new_profit', 20, 2)->default(0);
            $table->integer('eta_difference_hours')->default(0);
            $table->integer('risk_difference')->default(0);
            $table->string('status')->default('Pending Approval');
            $table->foreignUuid('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        // Redirect Logs (Execution tracking)
        Schema::create('redirect_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('redirect_request_id')->constrained('redirect_requests')->cascadeOnDelete();
            $table->string('event'); // Initiated, Notified Port, Route Updated, Completed
            $table->text('details')->nullable();
            $table->timestamps();
        });

        // Shipment Health Scores (Historical tracking of a shipment's health)
        Schema::create('shipment_health_scores', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('shipment_id')->constrained('shipments')->cascadeOnDelete();
            $table->integer('route_efficiency_score')->default(0);
            $table->integer('delivery_performance_score')->default(0);
            $table->integer('profitability_score')->default(0);
            $table->integer('risk_score')->default(0);
            $table->integer('documentation_score')->default(0);
            $table->integer('weather_score')->default(0);
            $table->integer('port_status_score')->default(0);
            $table->integer('overall_health_score')->default(0);
            $table->timestamp('calculated_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_health_scores');
        Schema::dropIfExists('redirect_logs');
        Schema::dropIfExists('redirect_requests');
        Schema::dropIfExists('decision_histories');
        Schema::dropIfExists('ai_decisions');
    }
};
