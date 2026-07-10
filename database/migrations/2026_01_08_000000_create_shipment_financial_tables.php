<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Shipment Invoices
        Schema::create('shipment_invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('shipment_id')->constrained('shipments')->cascadeOnDelete();
            $table->string('invoice_number')->unique();
            $table->string('type'); // Receivable, Payable
            $table->decimal('amount', 20, 2);
            $table->string('currency', 3)->default('USD');
            $table->date('invoice_date');
            $table->date('due_date');
            $table->string('status')->default('Pending'); // Pending, Partially Paid, Paid, Overdue
            $table->timestamps();
        });

        // Shipment Payments
        Schema::create('shipment_payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('invoice_id')->constrained('shipment_invoices')->cascadeOnDelete();
            $table->string('payment_method'); // Bank Transfer, LC, CAD, etc.
            $table->decimal('amount', 20, 2);
            $table->string('currency', 3)->default('USD');
            $table->decimal('exchange_rate', 15, 6)->default(1.0);
            $table->date('payment_date');
            $table->string('reference_number')->nullable();
            $table->timestamps();
        });

        // Shipment Contracts
        Schema::create('shipment_contracts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('shipment_id')->constrained('shipments')->cascadeOnDelete();
            $table->string('buyer_information')->nullable();
            $table->string('seller_information')->nullable();
            $table->string('incoterms')->nullable();
            $table->decimal('contract_value', 20, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->date('contract_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->timestamps();
        });

        // Shipment Insurance
        Schema::create('shipment_insurance', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('shipment_id')->constrained('shipments')->cascadeOnDelete();
            $table->string('insurance_company');
            $table->string('policy_number');
            $table->decimal('coverage_amount', 20, 2);
            $table->decimal('premium', 15, 2);
            $table->string('currency', 3)->default('USD');
            $table->date('expiry_date')->nullable();
            $table->string('claim_status')->nullable();
            $table->timestamps();
        });

        // Shipment Customs
        Schema::create('shipment_customs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('shipment_id')->constrained('shipments')->cascadeOnDelete();
            $table->string('country_id'); // Using string to avoid strict constraint if country deleted
            $table->string('clearance_type'); // Export, Import
            $table->string('inspection_status')->nullable();
            $table->decimal('tax_paid', 15, 2)->default(0);
            $table->decimal('duty_paid', 15, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->timestamp('cleared_at')->nullable();
            $table->timestamps();
        });

        // Update shipment_documents to support statuses
        Schema::table('shipment_documents', function (Blueprint $table) {
            $table->string('status')->default('Uploaded')->after('file_path'); // Uploaded, Verified, Approved, Rejected, Expired
            $table->date('expiry_date')->nullable()->after('status');
        });
        
        // Update shipment_financials to support detailed cost breakdown
        Schema::table('shipment_financials', function (Blueprint $table) {
            $table->string('currency', 3)->default('USD')->after('expected_profit');
            $table->decimal('exchange_rate', 15, 6)->default(1.0)->after('currency');
            $table->decimal('ocean_freight', 15, 2)->default(0)->after('exchange_rate');
            $table->decimal('land_transportation', 15, 2)->default(0)->after('ocean_freight');
            $table->decimal('loading_cost', 15, 2)->default(0)->after('land_transportation');
            $table->decimal('unloading_cost', 15, 2)->default(0)->after('loading_cost');
            $table->decimal('warehouse_cost', 15, 2)->default(0)->after('unloading_cost');
            $table->decimal('container_rental', 15, 2)->default(0)->after('warehouse_cost');
            $table->decimal('custom_clearance', 15, 2)->default(0)->after('container_rental');
            $table->decimal('import_duty', 15, 2)->default(0)->after('custom_clearance');
            $table->decimal('export_duty', 15, 2)->default(0)->after('import_duty');
            $table->decimal('bank_charges', 15, 2)->default(0)->after('export_duty');
            $table->decimal('inspection_fee', 15, 2)->default(0)->after('bank_charges');
            $table->decimal('actual_revenue', 20, 2)->default(0)->after('inspection_fee');
            $table->decimal('actual_cost', 20, 2)->default(0)->after('actual_revenue');
            $table->decimal('actual_profit', 20, 2)->default(0)->after('actual_cost');
            $table->decimal('profit_margin', 5, 2)->default(0)->after('actual_profit');
            $table->decimal('roi', 5, 2)->default(0)->after('profit_margin');
        });
    }

    public function down(): void
    {
        Schema::table('shipment_financials', function (Blueprint $table) {
            $table->dropColumn([
                'currency', 'exchange_rate', 'ocean_freight', 'land_transportation',
                'loading_cost', 'unloading_cost', 'warehouse_cost', 'container_rental',
                'custom_clearance', 'import_duty', 'export_duty', 'bank_charges',
                'inspection_fee', 'actual_revenue', 'actual_cost', 'actual_profit',
                'profit_margin', 'roi'
            ]);
        });
        
        Schema::table('shipment_documents', function (Blueprint $table) {
            $table->dropColumn(['status', 'expiry_date']);
        });

        Schema::dropIfExists('shipment_customs');
        Schema::dropIfExists('shipment_insurance');
        Schema::dropIfExists('shipment_contracts');
        Schema::dropIfExists('shipment_payments');
        Schema::dropIfExists('shipment_invoices');
    }
};
