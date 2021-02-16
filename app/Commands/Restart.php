<?php

namespace App\Commands;

use App\Finder;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class Restart extends Command
{
    use Finder;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'restart';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Restart a container';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $id = $this->finder('Restart a container');

        if ($id !== null) {
            $this->info('Restarting...');
            passthru(sprintf('docker container restart %s', $id));
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
