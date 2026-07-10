<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Shipment Containers
        Schema::create('shipment_containers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('shipment_id')->constrained('shipments')->cascadeOnDelete();
            $table->string('container_number');
            $table->string('container_type');
            $table->string('container_size');
            $table->string('seal_number')->nullable();
            $table->decimal('container_weight', 15, 2)->nullable();
            $table->timestamps();
        });

        // Shipment Financials
        Schema::create('shipment_financials', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('shipment_id')->constrained('shipments')->cascadeOnDelete();
            $table->decimal('commodity_value', 20, 2)->default(0);
            $table->decimal('freight_cost', 15, 2)->default(0);
            $table->decimal('insurance_cost', 15, 2)->default(0);
            $table->decimal('port_charges', 15, 2)->default(0);
            $table->decimal('tax', 15, 2)->default(0);
            $table->decimal('additional_charges', 15, 2)->default(0);
            $table->decimal('expected_revenue', 20, 2)->default(0);
            $table->decimal('expected_profit', 20, 2)->default(0);
            $table->timestamps();
        });

        // Shipment Documents
        Schema::create('shipment_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('shipment_id')->constrained('shipments')->cascadeOnDelete();
            $table->string('document_type'); // Bill of Lading, Invoice, etc.
            $table->string('file_name');
            $table->string('file_path');
            $table->foreignUuid('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Shipment Timelines
        Schema::create('shipment_timelines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('shipment_id')->constrained('shipments')->cascadeOnDelete();
            $table->string('milestone'); // Planning, Loading, Ocean Transit
            $table->text('notes')->nullable();
            $table->foreignUuid('responsible_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('occurred_at');
            $table->timestamps();
        });

        // Shipment Status Logs
        Schema::create('shipment_status_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('shipment_id')->constrained('shipments')->cascadeOnDelete();
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->text('reason')->nullable();
            $table->foreignUuid('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Shipment Histories (Audit)
        Schema::create('shipment_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('shipment_id')->constrained('shipments')->cascadeOnDelete();
            $table->string('action'); // Created, Updated, Redirected
            $table->text('changes')->nullable(); // JSON representation of changes
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Update main shipments table
        Schema::table('shipments', function (Blueprint $table) {
            $table->string('incoterm')->nullable()->after('shipment_type');
            $table->string('reference_number')->nullable()->after('incoterm');
            $table->string('hs_code')->nullable()->after('commodity_id');
            $table->integer('health_score')->default(100)->after('risk_score');
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn(['incoterm', 'reference_number', 'hs_code', 'health_score']);
        });

        Schema::dropIfExists('shipment_histories');
        Schema::dropIfExists('shipment_status_logs');
        Schema::dropIfExists('shipment_timelines');
        Schema::dropIfExists('shipment_documents');
        Schema::dropIfExists('shipment_financials');
        Schema::dropIfExists('shipment_containers');
    }
};
