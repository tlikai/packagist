<?php

namespace Packagist\Commands;

use Requests;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class FetchPackageCommand extends Command
{
    protected $name = 'fetch:package';

    protected $description = 'Fetch package from packagist';

    protected $baseUrl = 'https://packagist.org/packages/{package}.json';

    public function fire()
    {
        $verbose = $this->getOption('verbose');
        try {
            $package = $this->getArgument('name');
            if (!preg_match('/([\w_]+)\/([\w_]+)/', $package, $match)) {
                $this->error("Invalid {$package}");
                return;
            }
            list(, $vendor, $name) = $match;

            $url = str_replace('{package}', $package, $this->baseUrl);
            $verbose && $this->info("Fetch package from {$url}");
            $config = $this->getApplication()->getConfig('request');
            $response = Requests::get($url, $config['headers'], $config['options']);
            if ($response->status_code != 200) {
                $this->error('Request error!');
                return;
            }

            $basePath = $this->getApplication()->getConfig('publicPath') . '/' .$vendor;
            !is_dir($basePath)  && mkdir($basePath, 0755, true);
            file_put_contents($basePath . '/' . $name . '.json', $response->body);
        } catch(Exception $e) {
            $this->error($e->getMessage());
        }
    }


    public function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'name of package']
        ];
    }
}
