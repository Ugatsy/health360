<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('emergency_alerts', function (Blueprint $table) {
            $table->timestamp('sms_sent_at')->nullable()->after('resolution');
            $table->enum('sms_delivery_status', ['pending', 'delivered', 'failed', 'partial'])
                ->default('pending')
                ->after('sms_sent_at');
            $table->unsignedInteger('contacts_notified_count')->default(0)->after('sms_delivery_status');
            $table->text('sms_error_message')->nullable()->after('contacts_notified_count');
        });
    }

    public function down(): void
    {
        Schema::table('emergency_alerts', function (Blueprint $table) {
            $table->dropColumn(['sms_sent_at', 'sms_delivery_status', 'contacts_notified_count', 'sms_error_message']);
        });
    }
};
