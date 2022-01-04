<?php

namespace App\Commands;

use App\Finder;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Collection;
use LaravelZero\Framework\Commands\Command;

class SSHImage extends Command
{
    use Finder;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'ssh-image';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'SSH into an Image';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $image = $this->ask('Image name');

        if ($image !== null) {
            $this->info('Connecting...');
            passthru("docker run --rm -it -e COLUMNS=\"`tput cols`\" -e LINES=\"`tput lines`\" {$image} bash");
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
