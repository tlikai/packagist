<?php

namespace Packagist\Commands;

use Requests;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ImportCommand extends Command
{
    protected $name = 'import';

    protected $description = 'Import packages from packagist';

    public function fire()
    {
        $verbose = $this->getOption('verbose');
        $input = new ArrayInput([
            '-v' => $verbose,
            '',
        ]);

        $command = $this->getApplication()->find('fetch:index');
        $code = $command->run($input, $this->output);
        if ($code != 0) {
            $this->error("Fetch index error");
            return;
        }
        $this->info("Fetch index finish");
        
        $command = $this->getApplication()->find('fetch:list');
        $code = $command->run($input, $this->output);
        if ($code != 0) {
            $this->error("Fetch all error");
            return;
        }

        $this->info("Fetch all finish");
        $file = $this->getApplication()->getConfig('publicPath') . '/packages/list.json';
        $data = file_get_contents($file);
        $packages = json_decode($data, true);
        $command = $this->getApplication()->find('fetch:package');
        foreach ($packages['packageNames'] as $package) {
            $input = new ArrayInput([
                '-v' => $verbose,
                'name' => $package,
                '',
            ]);
            $code = $command->run($input, $this->output);
            $code ? $this->error("Fetched package {$package} error") : $this->info("Fetch package {$package}");
        }
    }
}
