<?php

namespace Packagist\Commands;

use Requests;
use Packagist\Helpers\Downloader;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class FetchPackageCommand extends Command
{

    protected $name = 'fetch:package';

    protected $description = 'Fetch package from packagist.org';

    public function fire()
    {
        $package = $this->getArgument('name');
        $verbose = $this->getOption('verbose');
        if (!preg_match('/([\w_]+)\/([\w_]+)/', $package, $match)) {
            $this->error("Invalid {$package}");
            return;
        }
        list(, $vendor, $name) = $match;

        $file = "/packages/{$package}.json";
        $verbose && $this->info("Downloading {$file}");
        Downloader::download($file);
    }

    public function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'name of package']
        ];
    }

}
