<?php

namespace App\Commands;

use App\Finder;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class Kill extends Command
{
    use Finder;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'kill';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Kill a container';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $id = $this->finder('Kill a container');

        if ($id !== null) {
            $this->info('Killing...');
            passthru(sprintf('docker container kill %s', $id));
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
