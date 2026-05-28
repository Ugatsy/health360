<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emergency_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('symptom_session_id')->constrained('symptom_sessions');
            $table->string('trigger_keyword', 255);
            $table->text('user_symptom_text');
            $table->enum('action_taken', ['displayed_emergency_message', 'sent_sms_alert', 'called_emergency_contact', 'displayed_911']);
            $table->boolean('emergency_contact_notified')->default(false);
            $table->string('emergency_contact_phone', 50)->nullable();
            $table->text('resolution')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emergency_alerts');
    }
};
