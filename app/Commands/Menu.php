<?php

namespace App\Commands;

use App\Finder;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;

class Menu extends Command
{
    use Finder;

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
        $context = trim(shell_exec('docker context show 2>&1'));
        $composeFound = file_exists(getcwd() . '/docker-compose.yml');

        $title = "Dock";

        if ($dockerIsRunning) {
            $title .= ' | Context: ' . $context;

            $hasContainers = collect(explode(PHP_EOL, shell_exec('docker ps -q')))->filter(function ($container) {
                return $container !== '';
            })->count();
            $title .= ' | Containers: ' . $hasContainers;
        }

        if ($composeFound) {
            $title .= ' | docker-compose found';
        }

        $menu = $this->menu($title)
            ->setWidth($this->menu()->getTerminal()->getWidth())
            ->setForegroundColour('15', 'white')
            ->setBackgroundColour('21', 'blue');


        if ($dockerIsRunning) {
            if ($hasContainers) {
                $menu->addStaticItem('Containers');
                $menu->addOption(Terminal::class, 'Connect to a container');
                $menu->addOption(Kill::class, 'Kill a container');
                $menu->addOption(Restart::class, 'Restart a container');
                $menu->addLineBreak(' ', 1);
            }

            if ($composeFound) {
                $menu->addStaticItem('Docker Compose');
                $menu->addOption(ComposeUp::class, 'docker compose up');
                $menu->addOption(ComposeDown::class, 'docker compose down');
                $menu->addOption(ComposeRestart::class, 'docker compose restart');
                $menu->addOption(ComposeLogs::class, 'docker compose logs');
                $menu->addOption(ComposePull::class, 'docker compose pull');
                $menu->addLineBreak(' ', 1);
            }
        } elseif (is_dir('/Applications/Docker.app') || is_dir('/Applications/OrbStack.app')) {
            $menu = $this->menu('Dock (Docker is not started)')
                ->setWidth($this->menu()->getTerminal()->getWidth())
                ->setForegroundColour('255', 'white')
                ->setBackgroundColour('196', 'red');

            $menu->addOption(StartDockerDesktop::class, (new StartDockerDesktop())->getDescription());
            $menu->addOption(StartOrbStack::class, (new StartOrbStack())->getDescription());
            $menu->addLineBreak(' ', 1);
        }

        $choice = $menu->open();

        if ($choice === null) {
            exit();
        }

        Artisan::call($choice, [], $this->getOutput());

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
