<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vessel_positions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('vessel_id')->index();
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->decimal('speed', 5, 2)->nullable();
            $table->integer('heading')->nullable();
            $table->integer('course')->nullable();
            $table->string('nav_status')->nullable();
            $table->timestamp('timestamp');
            $table->string('ais_provider')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vessel_positions');
    }
};
