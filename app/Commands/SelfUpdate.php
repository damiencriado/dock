<?php

namespace App\Commands;

use Exception;
use Illuminate\Support\Arr;
use LaravelZero\Framework\Commands\Command;

class SelfUpdate extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'self-update';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Update Dock';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        $oldVersion = $this->app->version();
        [$newVersion, $releaseUrl] = $this->getReleaseUrl();

        if ($oldVersion !== $newVersion) {
            $this->alert(sprintf('Update available from %s to %s', $oldVersion, $newVersion));
            if ($this->confirm('Do you want to update', 'yes')) {
                $this->task('Downloading latest release', function () use ($releaseUrl) {
                    try {
                        $binary = file_get_contents($releaseUrl);
                        file_put_contents($this->getTempPharFile(), $binary);

                        return true;
                    } catch (Exception $e) {
                        return false;
                    }
                });

                $this->task('Validate phar', function () {
                    try {
                        chmod($this->getTempPharFile(), fileperms($this->getLocalPharFile()));

                        return true;
                    } catch (Exception $e) {
                        return false;
                    }
                });

                $this->task('Replace old phar with new phar', function () {
                    try {
                        $this->replacePhar();

                        return true;
                    } catch (Exception $e) {
                        return false;
                    }
                });

                $this->info(sprintf(
                    'Updated from %s to %s.',
                    $oldVersion,
                    $newVersion
                ));

                exit();
            }
        } else {
            $this->info('You have the latest version installed.');
            if ($this->confirm('Press Enter to continue', 'yes')) {
                //
            }
        }
    }

    /**
     * @return array
     */
    protected function getReleaseUrl(): array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, sprintf('https://api.github.com/repos/%s/%s/releases/latest', config('self-update.vendor'), config('self-update.repo')));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36');

        $jsonResponse = curl_exec($ch);
        if (! $jsonResponse) {
            die('Error: "'.curl_error($ch).'" - Code: '.curl_errno($ch));
        }
        curl_close($ch);

        $response = json_decode($jsonResponse, true);

        return [Arr::get($response, 'tag_name'), Arr::get($response, 'assets.0.browser_download_url')];
    }

    /**
     * @return string
     */
    protected function getLocalPharFile(): string
    {
        return realpath($_SERVER['argv'][0]) ?? $_SERVER['argv'][0];
    }

    /**
     * @return string
     */
    protected function getTempPharFile(): string
    {
        return $this->getLocalPharFile().'-tmp';
    }

    private function replacePhar(): void
    {
        rename($this->getTempPharFile(), $this->getLocalPharFile());
    }
}
