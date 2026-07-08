<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Users
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('company_name')->nullable();
            $table->string('company_logo')->nullable();
            $table->string('language')->default('en');
            $table->string('timezone')->default('UTC');
            $table->string('notification_preference')->default('all');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignUuid('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // 2. Countries
        Schema::create('countries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('code')->unique();
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Ports
        Schema::create('ports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('country_id')->constrained('countries')->onDelete('cascade');
            $table->string('name');
            $table->string('code')->unique();
            $table->timestamps();
            $table->softDeletes();
        });

        // 4. Ships
        Schema::create('ships', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('imo_number')->unique()->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 5. Containers
        Schema::create('containers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('container_number')->unique();
            $table->string('type')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 6. Shipments (as specified by user)
        Schema::create('shipments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('shipment_code')->unique();
            $table->foreignUuid('user_id')->constrained('users');
            $table->foreignUuid('ship_id')->nullable()->constrained('ships');
            $table->foreignUuid('container_id')->nullable()->constrained('containers');
            
            $table->string('shipment_type')->default('export'); // import, export
            
            $table->foreignUuid('origin_country_id')->nullable()->constrained('countries');
            $table->foreignUuid('destination_country_id')->nullable()->constrained('countries');
            $table->foreignUuid('origin_port_id')->nullable()->constrained('ports');
            $table->foreignUuid('destination_port_id')->nullable()->constrained('ports');
            
            $table->foreignUuid('current_country_id')->nullable()->constrained('countries');
            $table->foreignUuid('current_port_id')->nullable()->constrained('ports');
            
            $table->timestamp('departure_date')->nullable();
            $table->timestamp('estimated_arrival')->nullable();
            $table->timestamp('actual_arrival')->nullable();
            
            $table->decimal('shipping_cost', 15, 2)->default(0);
            $table->decimal('insurance_cost', 15, 2)->default(0);
            
            $table->string('status')->default('pending');
            $table->integer('risk_score')->default(0);
            
            $table->decimal('latitude', 10, 6)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();
            
            $table->text('remarks')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });

        // 7. Shipment Histories
        Schema::create('shipment_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('shipment_id')->constrained('shipments')->onDelete('cascade');
            $table->string('status');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        
        // Caches and Jobs
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });
        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });
        Schema::create('jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('shipment_histories');
        Schema::dropIfExists('shipments');
        Schema::dropIfExists('containers');
        Schema::dropIfExists('ships');
        Schema::dropIfExists('ports');
        Schema::dropIfExists('countries');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
