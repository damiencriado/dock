<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;

class StartOrbStack extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'orbstack:start';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Start OrbStack';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->task('OrbStack is starting', function () {
            shell_exec('open /Applications/OrbStack.app');

            while (! Str::contains(shell_exec('docker info 2>&1'), 'Containers:')) {
                sleep(1);
            }

            return true;
        });


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
