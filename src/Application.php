<?php namespace Packagist;

use Symfony\Component\Console\Application as ConsoleApplication;

class Application extends ConsoleApplication
{
    protected $basePath;

    public function __construct($name = 'Packgist', $version = '1.0.0')
    {
        parent::__construct($name, $version);
    }

    public function getDefaultCommands()
    {
        return array_merge(parent::getDefaultCommands(), array(
            new Commands\FetcherCommand,
        ));
    }

    public function setBasePath($path)
    {
        $this->basePath = $path;
    }

    public function getBasePath()
    {
        return $this->basePath;
    }
}
