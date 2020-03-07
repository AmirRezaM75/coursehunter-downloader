<?php

use App\Downloader;
use GuzzleHttp\Client;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

$client = new Client(['base_url' => 'https://coursehunter.net']);
$adapter = new Local(__DIR__.'/Downloads');
$filesystem = new FileSystem($adapter);

return new Downloader($client, $filesystem);