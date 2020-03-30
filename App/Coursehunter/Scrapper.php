<?php


namespace App\Coursehunter;


use App\Html\Parser;
use App\Utility\Utility;
use GuzzleHttp\Client;
use League\Flysystem\Filesystem;

class Scrapper extends Application implements ApplicationInterface
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
        if (file_exists($path = $this->getCachedItemsPath())) {
            $cache = require $path;
        }

        $this->authenticate($this->options['email'], $this->options['password']);

        Utility::box('Scrapping...');

        $lastPage = $this->coursehunter->getLastPage();

        for ($page = 1; $page <= $lastPage; $page++) {
            Utility::write("page {$page} / $lastPage");

            $html = $this->coursehunter->getArchiveHTML($page);

            $courses = Parser::getCourseNamesURL($html);

            foreach ($courses as $slug) {
                if (isset($cache) and isset($cache[$slug])) {
                    Utility::write("$slug exists on cache file");
                    continue;
                }

                Utility::write($slug);

                $courseHTML = $this->coursehunter->getCourseHTML($slug);

                $basicInformation[$slug] = Parser::getBasicInformation($courseHTML);

                $this->system->cacheItems($this->getCachedItemsPath(), $basicInformation);
            }
        }
    }
}
