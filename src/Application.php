<?php

namespace Packagist;

use Symfony\Component\Console\Application as ConsoleApplication;

class Application extends ConsoleApplication
{
    protected $name = 'Packgist mirror tools';

    protected $version = '1.0.0';

    protected $config = [];

    public function __construct(array $config)
    {
        parent::__construct($this->name, $this->version);
        $this->config = $config;
        Helpers\Downloader::$basePath = $config['publicPath'];
        if (! empty($config['downloader']['headers'])) {
            Helpers\Downloader::$headers = array_merge($config['downloader']['headers'], Helpers\Downloader::$headers);
        }
        if (! empty($config['downloader']['options'])) {
            Helpers\Downloader::$options = array_merge($config['downloader']['options'], Helpers\Downloader::$options);
        }

    }

    public function getDefaultCommands()
    {
        return array_merge(parent::getDefaultCommands(), array(
            new Commands\ImportCommand,
            new Commands\FetchIndexCommand,
            new Commands\FetchListCommand,
            new Commands\FetchPackageCommand,
        ));
    }

    public function getConfig($name = null)
    {
        if (empty($name)) {
            return $this->config;
        }

        return isset($this->config[$name]) ? $this->config[$name] : null;
    }
}
