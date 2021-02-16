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
        $composeFound = file_exists(getcwd().'/docker-compose.yml');

        $title = "Dock";

        if ($dockerIsRunning) {
            $count = collect(explode(PHP_EOL, shell_exec('docker ps -q')))->filter(function ($container) {
                return $container !== '';
            })->count();
            $title .= ' | Containers: '.$count;
        }

        if ($composeFound) {
            $title .= ' | docker-compose found';
        }

        $menu = $this->menu($title)
            ->setWidth($this->menu()->getTerminal()->getWidth())
            ->setForegroundColour('15', 'white')
            ->setBackgroundColour('21', 'blue');

        if ($dockerIsRunning) {
            if ($count) {
                $menu->addLineBreak(' ', 1);
                $menu->addOption(SSH::class, (new SSH())->getDescription());
                $menu->addOption(Kill::class, (new Kill())->getDescription());
                $menu->addOption(Restart::class, (new Restart())->getDescription());
                $menu->addLineBreak(' ', 1);
            }

            if ($composeFound) {
                $menu->addOption(ComposeUp::class, (new ComposeUp())->getDescription());
                $menu->addOption(ComposeDown::class, (new ComposeDown())->getDescription());
                $menu->addOption(ComposeRestart::class, (new ComposeRestart())->getDescription());
                $menu->addOption(ComposeLogs::class, (new ComposeLogs())->getDescription());
                $menu->addOption(ComposePull::class, (new ComposePull())->getDescription());
                $menu->addLineBreak(' ', 1);
            }

            $menu->addOption(QuitDockerDesktop::class, (new QuitDockerDesktop())->getDescription());
        } elseif (is_dir('/Applications/Docker.app')) {
            $menu = $this->menu('Dock (Docker is not started)')
                ->setForegroundColour('255', 'white')
                ->setBackgroundColour('196', 'red');

            if ($composeFound) {
                $menu->addOption(StartDockerDesktopAndComposeUp::class,
                    (new StartDockerDesktopAndComposeUp())->getDescription());
            }
            $menu->addOption(StartDockerDesktop::class, (new StartDockerDesktop())->getDescription());
        }
        $menu->addOption(SelfUpdate::class, (new SelfUpdate())->getDescription());

        $menu->addLineBreak(' ', 1);

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
