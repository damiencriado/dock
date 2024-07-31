<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;

class StartDockerDesktopAndComposeUp extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'docker:start-up';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Start Docker Desktop and Compose Up';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->task('Docker Desktop is starting', function () {
            shell_exec('open /Applications/Docker.app');

            while (! Str::contains(shell_exec('docker info 2>&1'), 'Containers:')) {
                sleep(1);
            }

            return true;
        });

        passthru('docker compose up -d');
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
