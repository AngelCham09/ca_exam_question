<?php

namespace App\Console\Commands;

use App\Services\ExamApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncExamData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:sync-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and store data from the Cardinal Alpha API';

    /**
     * Execute the console command.
     */
    public function handle(ExamApiService $examApiService)
    {
        $this->info("Initializing Sync Sequence...");

        try {
            $examApiService->syncAll(function (string $message) {
                $this->line(" <info>✔</info> $message");
            });

            $this->info("Sync Completed Successfully!");
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("Sync Failed: " . $e->getMessage());
            Log::error("API Sync Error: " . $e->getTraceAsString());
            return Command::FAILURE;
        }
    }
}
