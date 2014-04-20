<?php

namespace Packagist\Commands;

use Requests;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ImportCommand extends Command
{
    protected $name = 'import';

    protected $description = 'Import packages from packagist.org';

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
            $this->error("Fetch package index faild");
            return;
        }
        $this->info("Fetch package index finish");

        $command = $this->getApplication()->find('fetch:list');
        $code = $command->run($input, $this->output);
        if ($code != 0) {
            $this->error("Fetch package list faild");
            return;
        }
        $this->info("Fetch package list finish");

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
            $code ? $this->error("Fetched package {$package} faild") : $this->info("Fetch package {$package}");
        }
    }
}
