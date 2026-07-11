<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vessels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('shipping_line_id')->nullable();
            $table->string('name');
            $table->string('imo_number')->unique()->index();
            $table->string('mmsi')->unique()->index();
            $table->string('call_sign')->nullable();
            $table->string('vessel_type')->default('Cargo');
            $table->integer('build_year')->nullable();
            $table->integer('length')->nullable();
            $table->integer('beam')->nullable();
            $table->integer('gross_tonnage')->nullable();
            $table->integer('deadweight_tonnage')->nullable();
            $table->string('status')->default('Active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vessels');
    }
};
