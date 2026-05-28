<?php

namespace App\Console\Commands;

use App\Services\SMSService;
use Illuminate\Console\Command;

class TestTextBee extends Command
{
    protected $signature = 'textbee:test {phone : Phone number to send test SMS to}';
    protected $description = 'Test TextBee SMS sending';

    public function handle(SMSService $smsService): int
    {
        $phone = $this->argument('phone');

        $this->info("Testing TextBee SMS to: {$phone}");

        $result = $smsService->send($phone, 'Health360 Test: Your emergency alert system is working correctly. No action needed.');

        if ($result) {
            $this->info('SMS sent successfully!');
            return Command::SUCCESS;
        }

        $this->error('SMS failed. Check logs for details.');
        return Command::FAILURE;
    }
}
