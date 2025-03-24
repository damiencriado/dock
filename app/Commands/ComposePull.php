<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class ComposePull extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'compose:pull';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Pull service images';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        passthru('docker compose pull');
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
