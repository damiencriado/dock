<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class ComposeDown extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'compose:down';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = '[Compose] Down';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        passthru('docker compose down --remove-orphans');
    }

    /**
     * Define the command's schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
