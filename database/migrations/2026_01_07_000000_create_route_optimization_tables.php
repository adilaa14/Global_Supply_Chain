<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Shipment Routes (Current Active Route)
        Schema::create('shipment_routes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('shipment_id')->constrained('shipments')->cascadeOnDelete();
            $table->text('waypoints'); // JSON array of lat/lng
            $table->decimal('total_distance', 15, 2)->default(0); // Nautical miles
            $table->decimal('distance_remaining', 15, 2)->default(0);
            $table->decimal('estimated_fuel', 15, 2)->default(0); // Tons
            $table->integer('estimated_time')->default(0); // Hours
            $table->integer('piracy_risk_score')->default(0);
            $table->integer('weather_risk_score')->default(0);
            $table->timestamps();
        });

        // Route Simulations (Alternative Routes)
        Schema::create('route_simulations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('shipment_id')->constrained('shipments')->cascadeOnDelete();
            $table->foreignUuid('alternative_country_id')->constrained('countries');
            $table->foreignUuid('alternative_port_id')->constrained('ports');
            $table->text('waypoints'); // JSON array of lat/lng
            $table->decimal('distance', 15, 2)->default(0);
            $table->decimal('fuel_consumption', 15, 2)->default(0);
            $table->integer('travel_time')->default(0); // Hours
            $table->integer('ocean_risk')->default(0);
            $table->integer('weather_risk')->default(0);
            $table->decimal('carbon_emission', 15, 2)->default(0);
            $table->timestamps();
        });

        // Shipment Route Histories
        Schema::create('shipment_route_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('shipment_id')->constrained('shipments')->cascadeOnDelete();
            $table->foreignUuid('old_route_id')->nullable()->constrained('shipment_routes')->nullOnDelete();
            $table->text('old_waypoints')->nullable();
            $table->text('new_waypoints');
            $table->string('reason');
            $table->foreignUuid('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_route_histories');
        Schema::dropIfExists('route_simulations');
        Schema::dropIfExists('shipment_routes');
    }
};
