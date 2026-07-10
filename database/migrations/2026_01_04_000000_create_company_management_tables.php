<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Company Documents
        Schema::create('company_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('document_type'); // Business License, Import License, etc.
            $table->string('document_name');
            $table->string('file_path');
            $table->timestamp('expiry_date')->nullable();
            $table->foreignUuid('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Company Settings
        Schema::create('company_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('setting_key');
            $table->text('setting_value')->nullable();
            $table->timestamps();
            
            $table->unique(['company_id', 'setting_key']);
        });

        // Add additional fields to companies table as requested by PRD Part 3
        Schema::table('companies', function (Blueprint $table) {
            $table->string('province')->nullable()->after('country_id');
            $table->string('default_currency', 3)->default('USD')->after('website');
            $table->string('timezone')->default('UTC')->after('default_currency');
            $table->string('preferred_language', 2)->default('en')->after('timezone');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['province', 'default_currency', 'timezone', 'preferred_language']);
        });
        
        Schema::dropIfExists('company_settings');
        Schema::dropIfExists('company_documents');
    }
};
