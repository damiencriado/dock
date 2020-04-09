<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Collection;
use LaravelZero\Framework\Commands\Command;

class Kill extends Command
{
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
        $containers = $this->getContainers();
        $menu = $containers->map(static function ($args) {
            return $args[1];
        })->toArray();

        $option = $this->menu('Select container to kill', $menu)
            ->setExitButtonText('Back')
            ->addLineBreak(' ', 1)
            ->open();

        if ($option !== null) {
            $selectedContainer = $containers->get($option);

            $this->info('Killing...');
            passthru(sprintf('docker container kill %s', $selectedContainer[0]));
        }
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    private function getContainers(): Collection
    {
        $shell = shell_exec("docker container ls --format '{{.ID}} {{.Names}}'");

        return collect(explode(PHP_EOL, $shell))->filter(static function ($line) {
            return $line !== '';
        })->map(static function ($line) {
            return explode(' ', $line);
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