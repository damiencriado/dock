<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;

class StartDockerForMac extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'docker:start';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Start Docker for Mac';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        shell_exec('open /Applications/Docker.app');
        $this->info('Docker for Mac is starting...');

        while (! Str::contains(shell_exec('docker info 2>&1'), 'Containers:')) {
            sleep(1);
        }
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
