<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('symptom_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->uuid('session_uuid')->unique();
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->enum('status', ['active', 'completed', 'abandoned', 'emergency_routed'])->default('active');
            $table->enum('device_type', ['web', 'ios', 'android'])->nullable();
            $table->string('app_version', 20)->nullable();
            $table->enum('highest_risk_level', ['emergency', 'high', 'medium', 'low', 'unknown'])->nullable();
            $table->boolean('was_emergency_detected')->default(false);
            $table->text('emergency_recommendation')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
            $table->index('session_uuid');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('symptom_sessions');
    }
};
