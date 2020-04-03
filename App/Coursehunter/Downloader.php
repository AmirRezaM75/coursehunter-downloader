<?php


namespace App\Coursehunter;

use App\Exceptions\OptionNotFoundException;
use App\Utility\Utility;

class Downloader extends Application implements ApplicationInterface
{
    public function start()
    {
        if (is_null($this->options['course']))
            throw new OptionNotFoundException('Course option (-c) is not provided');

        Utility::box('Start collecting local data');

        $localCourses = $this->filesystem->courses();

        Utility::box('Start collecting online data');

        // First we check cache file. If we have it, we load items.
        // so that is very quicker than scrapping HTML page.
        if (file_exists($cache = $this->getCachedItemsPath())) {
            $onlineCourses = require $cache;
        }

        if (! isset($onlineCourses[$this->options['course']])) {

            $this->authenticate($this->options['email'], $this->options['password']);

            Utility::write('Crawling page ...');

            $onlineCourses = $this->coursehunter->courseEpisodes($this->options['course']);

            $this->filesystem->cacheItems($this->basePath('Cache/items.php'), $onlineCourses);
        } else {
            Utility::write('Reading from cache file ...');
        }

        $this->filesystem->createFolderIfNotExists($this->options['course']);

        Utility::box('Downloading');

        $information = $onlineCourses[$this->options['course']];

        $link = str_replace('lesson1.mp4', '', $information['link']);

        for ($episode = 1; $episode <= $information['number']; $episode ++) {

            $downloadInformation = [
                'courseItems' => $information['number'],
                'courseName' => $this->options['course'],
                'episodeLink' => $link .  'lesson' . $episode . '.mp4',
                'episodeNumber' => $episode

            ];

            if (! isset($localCourses[$this->options['course']]) or
                ! in_array($episode, $localCourses[$this->options['course']]))
                $this->resolver->download($downloadInformation);
        }
    }
}
