<?php

namespace App\Commands;

use App\Finder;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Collection;
use LaravelZero\Framework\Commands\Command;

class Terminal extends Command
{
    use Finder;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'terminal';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Connect to a container';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $id = $this->finder('Terminal');

        if ($id !== null) {
            $this->info('Connecting...');
            passthru("docker exec -e COLUMNS=\"`tput cols`\" -e LINES=\"`tput lines`\" -ti {$id} bash");
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
