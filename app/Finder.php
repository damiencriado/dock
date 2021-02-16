<?php

namespace App;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use PhpSchool\CliMenu\CliMenu;
use PhpSchool\CliMenu\Input\InputResult;
use PhpSchool\CliMenu\MenuStyle;

trait Finder
{
    public function finder($title)
    {
        $containers = $this->getContainers();

        $choice = 'loop';
        $currentPage = 1;
        $perPage = 10;
        $filter = null;

        while ($choice === 'loop' || $choice === 'search') {
            $containersFiltered = $containers->filter(function ($item) use ($filter) {
                if (in_array($filter, ['', null], true)) {
                    return true;
                }

                return Str::contains($item['name'], $filter) || Str::contains($item['image'], $filter);
            });

            $paginator = new LengthAwarePaginator(
                $containersFiltered->forPage($currentPage, $perPage),
                $containersFiltered->count(),
                $perPage, $currentPage
            );

            $items = $paginator->getCollection()->map(static function ($args) {
                return sprintf('%s - %s', $args['name'], $args['image']);
            })->toArray();

            if ($filter) {
                $heading = "{$title} | Page: {$paginator->currentPage()}/{$paginator->lastPage()} | Results: {$containersFiltered->count()} | Matching: {$filter}";
            } else {
                $heading = "{$title} | Page: {$paginator->currentPage()}/{$paginator->lastPage()} | Results: {$containersFiltered->count()}";
            }

            $menu = $this->menu($heading)
                ->setExitButtonText('Back')
                ->setWidth($this->menu()->getTerminal()->getWidth())
                ->setForegroundColour('15', 'white')
                ->setBackgroundColour('21', 'blue');

            $menu->addOptions($items);

            // Actions Start
            $menu->addLineBreak(' ', 1);

            $menu->addOption('refresh', 'Refresh list');
            $menu->addItem('Search by name or image', function (CliMenu $cliMenu) use ($menu) {
                $popupStyle = (new MenuStyle)
                    ->setBg('164', 'magenta')
                    ->setFg('15', 'white');

                $result = $cliMenu->askText($popupStyle)
                    ->setPromptText('Search by name or image')
                    ->setValidator(function () {
                        return true;
                    })
                    ->ask();
                $menu->setResult($result);
                $cliMenu->close();
            });

            if (!$paginator->onFirstPage()) {
                $menu->addOption('previous', 'Previous page');
            }
            if ($paginator->hasMorePages()) {
                $menu->addOption('next', 'Next page');
            }

            $menu->addLineBreak(' ', 1);
            // Actions End

            $choice = $menu->open();

            if ($choice instanceof InputResult) {
                $filter = $choice->fetch();
                $currentPage = 1;
                $choice = 'loop';
            }

            if ($choice === 'next') {
                $currentPage++;
                $choice = 'loop';
            }

            if ($choice === 'previous') {
                $currentPage--;
                if ($currentPage < 1) {
                    $currentPage = 1;
                }
                $choice = 'loop';
            }

            if ($choice === 'refresh') {
                $containers = $this->getContainers();
                $currentPage = 1;
                $choice = 'loop';
            }
        }

        if ($choice !== null) {
            return $this->getContainers()->get($choice)['id'];
        }

        return null;
    }

    public function getContainers(): Collection
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
}
