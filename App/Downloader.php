<?php

namespace App;

use App\Utility\Utility;
use GuzzleHttp\Client;
use League\Flysystem\Filesystem;
use App\Filesystem\Controller as FilesystemController;
use App\Coursehunter\Controller as CoursehunterController;

class Downloader
{

    private $system;
    private $coursehunter;

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
        $this->coursehunter = new CoursehunterController($client);
    }

    public function start()
    {
        Utility::box('Start collecting local data');

        $localCourses = $this->system->courses();

        Utility::write('Finished');

        if (count($options = getopt("c:")) == 0) {
            Utility::write('No options provided');
            die;
        }

        if (! is_array($options['c']))
            $options['c'] = [$options['c']];

        $onlineCourseEpisodes = $this->coursehunter->courseEpisodes($options['c'][0]);

        Utility::box('Downloading');

        foreach ($onlineCourseEpisodes as $episode) {
            if (! in_array($episode['number'], $localCourses[$options['c'][0]])) {
                // download
            }
        }
    }
}