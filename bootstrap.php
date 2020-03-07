<?php

use App\Downloader;
use GuzzleHttp\Client;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

define('BASE_URL', 'https://coursehunter.net');

$client = new Client(['base_url' => BASE_URL]);
$adapter = new Local(__DIR__.'/Downloads');
$filesystem = new FileSystem($adapter);

return new Downloader($client, $filesystem);