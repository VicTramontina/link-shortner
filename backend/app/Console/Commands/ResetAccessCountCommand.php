<?php

namespace App\Console\Commands;

use App\Models\Link;
use Illuminate\Console\Command;

/**
 * Reset Access Count Command
 *
 * Resets the access count for all links on the first day of each month.
 */
class ResetAccessCountCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'links:reset-access-count';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset access count for all links (runs on the first day of each month)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Resetting access counts for all links...');

        $count = Link::query()->update(['access_count' => 0]);

        $this->info("Successfully reset access count for {$count} links.");

        return Command::SUCCESS;
    }
}
