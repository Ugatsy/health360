<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('symptom_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('symptom_sessions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('body_region_id')->constrained('body_regions');
            $table->text('symptom_text');
            $table->enum('pain_type', ['sharp', 'dull', 'burning', 'throbbing', 'pressure'])->nullable();
            $table->integer('pain_intensity')->nullable();
            $table->string('pain_duration', 100)->nullable();
            $table->json('additional_symptoms')->nullable();
            $table->datetime('symptom_started_at')->nullable();
            $table->timestamp('recorded_at')->useCurrent();
            $table->timestamps();

            $table->index('session_id');
            $table->index('user_id');
            $table->index('body_region_id');
            $table->index('pain_intensity');
            $table->index('recorded_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('symptom_entries');
    }
};
