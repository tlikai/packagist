<?php

namespace Packagist\Commands;

use Requests;

class FetchListCommand extends Command
{
    protected $name = 'fetch:list';

    protected $description = 'Fetch package list from packagist.org';

    public function fire()
    {
        $verbose = $this->getOption('verbose');

        $file = '/packages/list.json';
        $verbose && $this->info("Downloading {$url}");
        Download::download($file);
    }
}
