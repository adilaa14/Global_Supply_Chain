<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracking_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('vessel_id')->nullable()->index();
            $table->uuid('shipment_id')->nullable()->index();
            $table->uuid('port_id')->nullable()->index();
            $table->string('event_type');
            $table->text('description')->nullable();
            $table->timestamp('event_time');
            $table->decimal('location_lat', 10, 8)->nullable();
            $table->decimal('location_lng', 11, 8)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracking_events');
    }
};
