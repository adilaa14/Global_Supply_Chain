<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ais_provider_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('provider_name');
            $table->string('endpoint');
            $table->integer('status_code');
            $table->integer('response_time')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ais_provider_logs');
    }
};
