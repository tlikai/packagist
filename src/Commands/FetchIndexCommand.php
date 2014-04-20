<?php

namespace Packagist\Commands;

use Requests;
use Packagist\Helpers\Downloader;

class FetchIndexCommand extends Command
{
    protected $name = 'fetch:index';

    protected $description = 'Fetch package index from packagist.org';

    public function fire()
    {
        $verbose = $this->getOption('verbose');

        $file = '/packages.json';
        $verbose && $this->info("Downloading {$file}");
        $data = Downloader::fetch($file);
        if (!$data) {
            $this->error("Download: {$file}");
            exit(1);
        }

        $json = json_decode($data, true);
        foreach ($json['provider-includes'] as $file => $hash) {
            $file = str_replace('%hash%', $hash['sha256'], $file);
            $verbose && $this->info("Downloading {$file}");
            $data = Downloader::fetch($file);
            if (!$data) {
                $this->error("Download: {$file}");
                continue;
            }

            $providers = json_decode($data, true);
            foreach ($providers['providers'] as $vendor => $hashes) {
                $hash = $hashes['sha256'];
                $file = "/p/{$vendor}\${$hash}.json";
                $verbose && $this->info("Downloading {$file}");
                Downloader::download($file);
            }
        }
    }
}
