<?php

namespace Packagist\Commands;

use Requests;

class FetchCommand extends Command
{
    protected $name = 'fetch:all';

    protected $description = 'Fetch all packages from packagist';

    public function fire()
    {
        $verbose = $this->getOption('verbose');
        try {
            $url = 'https://packagist.org/packages/list.json';
            $verbose && $this->info("Fetch {$url}");
            $config = $this->getApplication()->getConfig('request');
            $response = Requests::get($url, $config['headers'], $config['options']);
            if ($response->status_code != 200) {
                $this->error('Request error!');
                return;
            }
            file_put_contents($this->getApplication()->getConfig('publicPath') . '/packages/list.json', $response->body);
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
