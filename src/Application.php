<?php

namespace Packagist;

use Symfony\Component\Console\Application as ConsoleApplication;

class Application extends ConsoleApplication
{
    protected $config = [];

    public function __construct($name = 'Packgist', $version = '1.0.0')
    {
        parent::__construct($name, $version);
    }

    public function getDefaultCommands()
    {
        return array_merge(parent::getDefaultCommands(), array(
            new Commands\FetchIndexCommand,
            new Commands\ImportCommand,
            new Commands\FetchCommand,
            new Commands\FetchPackageCommand,
        ));
    }

    public function setConfig($config)
    {
        $this->config = $config;
    }

    public function getConfig($name = null)
    {
        if (empty($name)) {
            return $this->config;
        } 
        return isset($this->config[$name]) ? $this->config[$name] : null;
    }
}
