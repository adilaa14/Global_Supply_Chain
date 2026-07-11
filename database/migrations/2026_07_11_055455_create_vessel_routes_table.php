<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vessel_routes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('vessel_id')->index();
            $table->uuid('origin_port_id')->nullable();
            $table->uuid('destination_port_id')->nullable();
            $table->timestamp('departure_time')->nullable();
            $table->timestamp('estimated_arrival')->nullable();
            $table->text('route_geometry')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vessel_routes');
    }
};
