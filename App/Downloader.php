<?php

namespace App;

use App\Http\Resolver;
use App\Utility\Utility;
use GuzzleHttp\Client;
use League\Flysystem\Filesystem;
use App\Filesystem\Controller as FilesystemController;
use App\Coursehunter\Controller as CoursehunterController;

class Downloader
{

    private $system;
    private $coursehunter;
    private $resolver;

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
        $this->resolver = new Resolver($client);
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

        $wantedCourse = $options['c'][0];

        $onlineCourseEpisodes = $this->coursehunter->courseEpisodes($wantedCourse);

        $this->system->createFolderIfNotExists($wantedCourse);

        Utility::box('Downloading');

        foreach ($onlineCourseEpisodes[$wantedCourse] as $episode) {
            if (!isset($localCourses[$wantedCourse]) or ! in_array($episode['number'], $localCourses[$wantedCourse])) {
                $this->resolver->download($episode, $wantedCourse);
            }
        }
    }
}