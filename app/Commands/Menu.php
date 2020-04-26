<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;

class Menu extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'menu';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Show GUI';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $dockerIsRunning = Str::contains(shell_exec('docker info 2>&1'), 'Containers:');
        $composeFound = file_exists(getcwd().'/docker-compose.yml');

        $commands = collect();

        if ($dockerIsRunning) {
            $count = collect(explode(PHP_EOL, shell_exec('docker ps -q')))->filter(function ($container) {
                return $container !== '';
            })->count();


            if ($count) {
                $commands->push(SSH::class);
                $commands->push(Kill::class);
                $commands->push(Restart::class);
            }

            if ($composeFound) {
                $commands->push(ComposeUp::class);
                $commands->push(ComposeDown::class);
                $commands->push(ComposeRestart::class);
                $commands->push(ComposeLogs::class);
                $commands->push(ComposePull::class);
            }
            $commands->push(SelfUpdate::class);
            $commands->push(QuitDockerDesktop::class);
        } elseif (is_dir('/Applications/Docker.app')) {
            $commands->push(StartDockerDesktop::class);
        }

        $options = $commands->map(static function ($command) {
            return (new $command)->getDescription();
        });

        $title = sprintf('Dock (%s %s running', $count ?? 0, Str::plural('container', $count ?? 0));
        if ($composeFound) {
            $title .= ', docker-compose found';
        }
        $title .= ')';

        $menu = $this->menu($title, $options->toArray())->addLineBreak(' ', 1);

        if (! $dockerIsRunning) {
            $menu = $this->menu('Dock (Docker is not started)', $options->toArray())
                ->setForegroundColour('white')
                ->setBackgroundColour('red');
        }

        $option = $menu->open();

        if ($option === null) {
            exit();
        }

        Artisan::call($commands[$option], [], $this->getOutput());

        $this->handle();
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
