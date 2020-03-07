<?php

namespace App;

use App\Utility\Utility;
use GuzzleHttp\Client;
use League\Flysystem\Filesystem;
use App\Filesystem\Controller as FilesystemController;

class Downloader
{

    private $system;

    /**
     * Dependencies Auto Injection
     *
     * @param Client $client
     * @param Filesystem $filesystem
     *
    */
    public function __construct(Client $client, Filesystem $filesystem)
    {
        $this->system = new FilesystemController($filesystem);
    }

    public function start()
    {
        Utility::box('Start collecting local data');
        $this->system->getSeries();
    }
}