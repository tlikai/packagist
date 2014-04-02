<?php namespace Packagist\Commands;

class FetcherCommand extends Command
{
    protected $name = 'fetch';

    protected $description = 'Fetch packagist all packages';

    public function fire()
    {
        echo 'fetching';
    }
}
