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
        $options = $this->standardizeOptions();

        if (is_null($options['course'])) {
            Utility::write('Course option (-c) is not provided');
            die;
        }

        Utility::box('Start collecting local data');

        $localCourses = $this->system->courses();

        Utility::box('Start collecting online data');

        // First we will see if we have a cache file. If we do, we load items from that file
        // so that is very quicker than scrapping HTML page.
        if (file_exists($cache = $this->getCachedItemsPath())) {
            $onlineCourseEpisodes = require $cache;
        }

        if (! isset($onlineCourseEpisodes[$options['course']])) {

            if (! is_null($options['username']) and ! is_null($options['password'])) {
                Utility::write('Authenticating ...');

                if (! $this->coursehunter->authenticate($options['username'], $options['password']))
                    throw new \LogicException('Something is wrong with your authentication credentials');
            }

            Utility::write('Crawling page ...');

            $onlineCourseEpisodes = $this->coursehunter->courseEpisodes($options['course']);

            $this->system->cacheItems($this->basePath('Cache/items.php'), $onlineCourseEpisodes);
        } else {
            Utility::write('Reading from cache file ...');
        }

        $this->system->createFolderIfNotExists($options['course']);

        Utility::box('Downloading');

        foreach ($onlineCourseEpisodes[$options['course']] as $episode) {
            if (! isset($localCourses[$options['course']]) or ! in_array($episode['number'], $localCourses[$options['course']])) {
                $this->resolver->download($episode, $options['course']);
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

    /**
     * Get all arguments from command line and make it more readable
     *
     * @return array
     */
    private function standardizeOptions() {
        $arguments = 'c:';
        $arguments .= 'u:';
        $arguments .= 'p:';
        $options = getopt($arguments);

        return [
            'course' =>
                isset($options['c'])
                ? trim(str_replace(BASE_URL . '/course/', '', $options['c']), '/')
                : null,
            'username' => $options['u'] ?? null,
            'password' => $options['p'] ?? null,
        ];
    }
}