<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Collection;
use LaravelZero\Framework\Commands\Command;

class SSH extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'ssh';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'SSH into a Container';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $containers = $this->getContainers();
        $menu = $containers->map(static function ($args) {
            return sprintf('%s - %s', $args['name'], $args['image']);
        })->toArray();

        $option = $this->menu('Containers', $menu)
            ->setExitButtonText('Back')
            ->addLineBreak(' ', 1)
            ->open();

        if ($option !== null) {
            $selectedContainer = $containers->get($option);

            $this->info('Connecting...');
            passthru(sprintf('docker exec -e COLUMNS="`tput cols`" -e LINES="`tput lines`" -ti %s bash', $selectedContainer['id']));
        }
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    private function getContainers(): Collection
    {
        $shell = shell_exec("docker container ls --format '{{.ID}} {{.Image}} {{.Names}}'");

        return collect(explode(PHP_EOL, $shell))->filter(static function ($line) {
            return $line !== '';
        })->map(static function ($line) {
            $attributes = explode(' ', $line);

            return [
                'id'    => $attributes[0],
                'image' => $attributes[1],
                'name'  => $attributes[2],
            ];
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
