<?php

namespace Packagist\Commands;

use Requests;

class FetchIndexCommand extends Command
{
    protected $name = 'fetch:index';

    protected $description = 'Fetch index packages from packagist';

    public function fire()
    {
        try {
            $data = $this->getPackages();
            if (empty($data)) {
                $this->error('Packagist json can not be empty');
                return;
            }
            $this->getProviderInclude($data);
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function getPackages()
    {
        $verbose = $this->getOption('verbose');
        $url = 'https://packagist.org/packages.json';
        $verbose && $this->info("Fetch {$url}");
        $config = $this->getApplication()->getConfig('request');
        $response = Requests::get($url, $config['headers'], $config['options']);
        if ($response->status_code != 200) {
            $this->error('Request error!');
            return;
        }
        file_put_contents($this->getApplication()->getConfig('publicPath') . '/packages.json', $response->body);
        return $response->body;
    }

    public function getProviderInclude($data)
    {
        $verbose = $this->getOption('verbose');
        // provider directory
        $pPath = $this->getApplication()->getConfig('publicPath') . '/p';
        if (!is_dir($pPath)) {
            mkdir($pPath, 0755, true);
        }

        $packages = json_decode($data, true);
        $config = $this->getApplication()->getConfig('request');
        foreach ($packages['provider-includes'] as $providerHashUrl => $hash) {
            $providers = str_replace('%hash%', $hash['sha256'], $providerHashUrl);
            $url = "https://packagist.org/{$providers}";
            $response = Requests::get($url, $config['headers'], $config['options']);
            if ($response->status_code != 200) {
                $this->error('Request error!');
                return;
            }
            file_put_contents($this->getApplication()->getConfig('publicPath') . '/' . $providers, $response->body);
            $verbose && $this->info("Fetch {$url}");
        }
    }
}
