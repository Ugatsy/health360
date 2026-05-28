<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestEmergencySms extends Command
{
    protected $signature = 'emergency:test-sms
                            {userId? : User ID to send to all their contacts}
                            {--contact= : Send to a specific contact ID instead}
                            {--phone= : Send to a raw phone number directly}
                            {--message= : Custom message (optional)}
                            {--legacy : Use old local gateway instead of TextBee cloud API}';

    protected $description = 'Test SMS delivery via TextBee gateway';

    public function handle(): int
    {
        $useLegacy = $this->option('legacy');

        if ($useLegacy) {
            return $this->handleLegacy();
        }

        return $this->handleTextBee();
    }

    protected function handleTextBee(): int
    {
        $apiKey = config('textbee.api_key');
        $deviceId = config('textbee.device_id');
        $apiUrl = config('textbee.api_url');

        $this->info('=== Health360 SMS Test (TextBee Cloud API) ===');
        $this->line("API URL: {$apiUrl}/{$deviceId}/send-sms");
        $this->line("");

        $targets = $this->resolveTargets();
        if ($targets === null) {
            return Command::FAILURE;
        }

        $message = $this->option('message') ?: config('sms.test_message');

        $this->warn('Sending SMS via TextBee cloud API...');
        $success = 0;
        $failed = 0;

        foreach ($targets as $target) {
            $this->line("");
            $this->line("--- Sending to: {$target['name']} ({$target['phone']}) ---");

            $start = microtime(true);
            try {
                $response = Http::timeout(config('textbee.timeout', 30))
                    ->withHeaders([
                        'x-api-key' => $apiKey,
                        'Content-Type' => 'application/json',
                    ])
                    ->post($apiUrl . '/' . $deviceId . '/send-sms', [
                        'recipients' => [$target['phone']],
                        'message' => $message,
                    ]);

                $elapsed = round((microtime(true) - $start) * 1000, 1);
                $this->line("  HTTP {$response->status()} ({$elapsed}ms)");
                $this->line("  Response body: " . $response->body());

                if ($response->successful()) {
                    $this->info("  SMS sent successfully");
                    $success++;
                } else {
                    $this->error("  SMS failed");
                    $failed++;
                }
            } catch (\Exception $e) {
                $elapsed = round((microtime(true) - $start) * 1000, 1);
                $this->error("  Exception after {$elapsed}ms: {$e->getMessage()}");
                $failed++;
            }
        }

        $this->line("");
        $this->warn('=== Results ===');
        $this->info("  Sent: {$success}");
        $this->info("  Failed: {$failed}");

        return $failed > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    protected function handleLegacy(): int
    {
        $gatewayUrl = config('sms.gateway_url');
        $from = config('sms.from_number');
        $timeout = config('sms.timeout', 10);

        $this->info('=== Health360 SMS Test (Legacy Gateway) ===');
        $this->line("Gateway URL: {$gatewayUrl}");
        $this->line("From number: {$from}");
        $this->line("");

        $this->warn('Step 1: Checking gateway connectivity...');
        try {
            $ping = Http::timeout(5)->get($gatewayUrl);
            $this->line("  HTTP Status: {$ping->status()}");
            $this->line("  Response: " . $ping->body());
        } catch (\Exception $e) {
            $this->error("  Gateway UNREACHABLE: {$e->getMessage()}");
            $this->line("  Tip: Ensure TextBee app is running on your Android phone");
            $this->line("  and the phone is connected to the same network.");
            if (!$this->confirm('Continue anyway?', false)) {
                return Command::FAILURE;
            }
        }
        $this->line("");

        $targets = $this->resolveTargets();
        if ($targets === null) {
            return Command::FAILURE;
        }

        $message = $this->option('message') ?: config('sms.test_message');

        $this->warn('Step 2: Sending SMS...');
        $success = 0;
        $failed = 0;

        foreach ($targets as $target) {
            $this->line("");
            $this->line("--- Sending to: {$target['name']} ({$target['phone']}) ---");

            $start = microtime(true);
            try {
                $response = Http::timeout($timeout)
                    ->post($gatewayUrl, [
                        'from' => $from,
                        'to' => $target['phone'],
                        'text' => $message,
                    ]);

                $elapsed = round((microtime(true) - $start) * 1000, 1);
                $this->line("  HTTP {$response->status()} ({$elapsed}ms)");
                $this->line("  Response body: " . $response->body());

                if ($response->successful()) {
                    $this->info("  SMS sent successfully");
                    $success++;
                } else {
                    $this->error("  SMS failed");
                    $failed++;
                }
            } catch (\Exception $e) {
                $elapsed = round((microtime(true) - $start) * 1000, 1);
                $this->error("  Exception after {$elapsed}ms: {$e->getMessage()}");
                $failed++;
            }
        }

        $this->line("");
        $this->warn('=== Results ===');
        $this->info("  Sent: {$success}");
        $this->info("  Failed: {$failed}");

        return $failed > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    protected function resolveTargets(): ?array
    {
        $targets = [];

        if ($phone = $this->option('phone')) {
            $targets[] = ['name' => 'Direct number', 'phone' => $phone];
        } elseif ($contactId = $this->option('contact')) {
            $contact = \App\Models\EmergencyContact::find($contactId);
            if (!$contact) {
                $this->error("Contact ID {$contactId} not found.");
                return null;
            }
            $targets[] = ['name' => $contact->name, 'phone' => $contact->phone_number];
        } elseif ($userId = $this->argument('userId')) {
            $user = User::with('emergencyContacts')->find($userId);
            if (!$user) {
                $this->error("User ID {$userId} not found.");
                return null;
            }
            if ($user->emergencyContacts->isEmpty()) {
                $this->warn("User {$user->name} has no emergency contacts.");
                return null;
            }
            foreach ($user->emergencyContacts as $c) {
                $targets[] = ['name' => $c->name, 'phone' => $c->phone_number];
            }
        } else {
            $this->error('Provide a userId, --contact, or --phone.');
            return null;
        }

        return $targets;
    }
}
