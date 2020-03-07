<?php

namespace App;

use App\Utility\Utility;
use GuzzleHttp\Client;
use League\Flysystem\Filesystem;

class Downloader
{
    /**
     * Dependencies Auto Injection
     *
     * @param Client $client
     * @param Filesystem $filesystem
     *
    */
    public function __construct(Client $client, Filesystem $filesystem)
    {
        
    }

    public function start()
    {
        Utility::box('Authenticating');
    }
}