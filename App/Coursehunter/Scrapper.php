<?php


namespace App\Coursehunter;


use App\Html\Parser;
use App\Utility\Utility;

class Scrapper extends Application implements ApplicationInterface
{
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

                $this->filesystem->cacheItems($this->getCachedItemsPath(), $basicInformation);
            }
        }
    }
}
