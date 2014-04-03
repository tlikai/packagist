<?php

namespace Packagist\Commands;

use Requests;

class FetchIndexCommand extends Command
{
    protected $name = 'fetch:index';

    protected $description = 'Fetch index packages from packagist';

    public function fire()
    {
        $verbose = $this->getOption('verbose');
        $verbose && $this->info("Fetch {$url}");
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
        $url = 'https://packagist.org/packages.json';
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
        // provider directory
        $pPath = $this->getApplication()->getConfig('publicPath') . '/p';
        if (!is_dir($pPath)) {
            mkdir($pPath, 0755, true);
        }

        $packages = json_decode($data, true);
        $config = $this->getApplication()->getConfig('request');
        foreach ($packages['provider-includes'] as $providerHashUrl => $hash) {
            $providers = str_replace('%hash%', $hash['sha256'], $providerHashUrl);
            $response = Requests::get("https://packagist.org/{$providers}", $config['headers'], $config['options']);
            if ($response->status_code != 200) {
                $this->error('Request error!');
                return;
            }
            file_put_contents($this->getApplication()->getConfig('publicPath') . '/' . $providers, $response->body);
        }
    }
}
