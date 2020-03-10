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
    private $basePath;

    /**
     * Dependencies Auto Injection
     *
     * @param Client $client
     * @param Filesystem $filesystem
     *
    */
    public function __construct(Client $client, Filesystem $filesystem)
    {
        $this->basePath = rtrim(dirname(__DIR__), '\/');
        $this->coursehunter = new CoursehunterController($client);
        $this->system = new FilesystemController($filesystem);
        $this->resolver = new Resolver($client);
    }


    public function start()
    {
        Utility::box('Start collecting local data');

        $localCourses = $this->system->courses();

        if (count($options = getopt("c:")) == 0) {
            Utility::write('No options provided');
            die;
        }

        if (! is_array($options['c']))
            $options['c'] = [$options['c']];

        $wantedCourse = $options['c'][0];

        Utility::box('Start collecting online data');

        // First we will see if we have a cache file. If we do, we load items from that file
        // so that is very quicker than scrapping HTML page.
        if (file_exists($cache = $this->getCachedItemsPath())) {
            $onlineCourseEpisodes = require $cache;
        }

        if (! isset($onlineCourseEpisodes[$wantedCourse])) {
            $onlineCourseEpisodes = $this->coursehunter->courseEpisodes($wantedCourse);

            $this->system->cacheItems($this->basePath('Cache/items.php'), $onlineCourseEpisodes);
        }

        $this->system->createFolderIfNotExists($wantedCourse);

        Utility::box('Downloading');

        foreach ($onlineCourseEpisodes[$wantedCourse] as $episode) {
            if (! isset($localCourses[$wantedCourse]) or ! in_array($episode['number'], $localCourses[$wantedCourse])) {
                $this->resolver->download($episode, $wantedCourse);
            }
        }
    }

    /**
     * Get the base path for the application
     *
     * @param string $path
     * @return string
     */
    public function basePath($path = '')
    {
        return $this->basePath . ($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Get the path to courses cache file
     *
     * @return string
     */
    private function getCachedItemsPath() {
        return $this->basePath('Cache/items.php');
    }
}