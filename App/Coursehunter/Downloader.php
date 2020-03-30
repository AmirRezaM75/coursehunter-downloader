<?php


namespace App\Coursehunter;

use App\Exceptions\OptionNotFoundException;
use App\Utility\Utility;
use GuzzleHttp\Client;
use League\Flysystem\Filesystem;

class Downloader extends Application implements ApplicationInterface
{
    private $options;

    /**
     * @param Client $client
     * @param Filesystem $filesystem
     * @param array $options
     * @param string $basePath
     */
    public function __construct(Client $client, Filesystem $filesystem, array $options, $basePath)
    {
        parent::__construct($client, $filesystem, $basePath);

        $this->options = $options;
    }

    public function start()
    {
        if (is_null($this->options['course']))
            throw new OptionNotFoundException('Course option (-c) is not provided');

        Utility::box('Start collecting local data');

        $localCourses = $this->system->courses();

        Utility::box('Start collecting online data');

        // First we check cache file. If we have it, we load items.
        // so that is very quicker than scrapping HTML page.
        if (file_exists($cache = $this->getCachedItemsPath())) {
            $onlineCourseEpisodes = require $cache;
        }

        if (! isset($onlineCourseEpisodes[$this->options['course']])) {

            $this->authenticate($this->options['email'], $this->options['password']);

            Utility::write('Crawling page ...');

            $onlineCourseEpisodes = $this->coursehunter->courseEpisodes($this->options['course']);

            $this->system->cacheItems($this->basePath('Cache/items.php'), $onlineCourseEpisodes);
        } else {
            Utility::write('Reading from cache file ...');
        }

        $this->system->createFolderIfNotExists($this->options['course']);

        Utility::box('Downloading');

        foreach ($onlineCourseEpisodes[$this->options['course']] as $episode)
            if (! isset($localCourses[$this->options['course']]) or
                ! in_array($episode['number'], $localCourses[$this->options['course']]))
                $this->resolver->download($episode, $this->options['course']);
    }
}
