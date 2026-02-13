<?php

namespace App\Console\Commands;

use App\Exceptions\MicroserviceException;
use App\Services\HelmetViolationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendHelmetViolations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-helmet-violations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send helmet violations to Telegram';

    /**
     * Execute the console command.
     * @throws MicroserviceException
     */
    public function handle(HelmetViolationService $helmetViolationService): void
    {
        $this->info('Fetching unsent violations...');

        $violations = $helmetViolationService->getUnsent();

        if (empty($violations)) {
            $this->info('No new violations.');
            return;
        }

        $sentIds = [];

        foreach ($violations as $violation) {
            try {
                $helmetViolationService->sendViolation($violation);
                $sentIds[] = $violation['id'];

                $this->info("Sent violation {$violation['id']}");

            } catch (\Exception $e) {
                $this->error("Failed to send violation {$violation['id']}: {$e->getMessage()}");
            }
        }
        Log::info('Scheduler triggered at ' . now());

        if (!empty($sentIds)) {
            $helmetViolationService->markAsSent($sentIds);
            $this->info('Marked violations as sent.');
        }
    }
}
