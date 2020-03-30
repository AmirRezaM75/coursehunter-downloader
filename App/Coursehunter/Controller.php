<?php

namespace App\Coursehunter;

use App\Exceptions\CourseNotFoundException;
use App\Exceptions\SubscriptionNotActiveException;
use App\Filesystem\Controller as FilesystemController;
use App\Html\Parser;
use App\Utility\Utility;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use League\Flysystem\Filesystem;

class Controller
{

    private $client;
    private $cookie;

    public function __construct(Client $client, Filesystem $filesystem)
    {
        $this->client = $client;
        $this->system = new FilesystemController($filesystem);
        $this->cookie = new CookieJar();
    }

    /**
     * @param string $username
     * @param string $password
     *
     * @return bool
     * @throws SubscriptionNotActiveException
     */
    public function authenticate($username, $password)
    {
        $response = $this->client->post(BASE_URL.'/sign-in', [
            'cookies' => $this->cookie,
            'body' => [
                'e_mail' => $username,
                'password' => $password,
                'remember' => 1
            ],
            'verify' => false
        ]);

        $html = $response->getBody()->getContents();

        if (strpos($html, 'Wrong password') !== false) {
            return false;
        }

        if (strpos($html, 'Go Premium')) {
            throw new SubscriptionNotActiveException('Subscription is not activated');
        }

        return true;
    }

    public function courseEpisodes($course)
    {
        $html = $this->getCourseHTML($course);

        $array[$course] = Parser::getEpisodesArray($html);

        return $array;
    }

    public function scrapSite($path)
    {
        $basicInformation = [];

        $lastPage = $this->getLastPage();

        for ($page = 1; $page <= $lastPage; $page++) {
            Utility::write("page {$page} / $lastPage");

            $html = $this->getArchiveHTML($page);

            $courses = Parser::getCourseNamesURL($html);

            foreach ($courses as $courseSlug) {
                Utility::write($courseSlug);
                $courseHTML = $this->getCourseHTML($courseSlug);

                $basicInformation[$courseSlug] = Parser::getBasicInformation($courseHTML);

                $this->system->cacheItems($path, $basicInformation);
            }
        }
    }

    public function getLastPage()
    {
        $html = $this->getArchiveHTML();

        return Parser::getLastPage($html);
    }


    /**
     * Get HTML of the archive page
     *
     * @param int $page
     * @return string
     */
        private function getArchiveHTML($page = 1)
    {
        return $this->client->get(BASE_URL . '/archive?page=' . $page, [
            'cookies' => $this->cookie,
            'verify' => false,
            'allow_redirects' => false
        ])->getBody()->getContents();
    }

    /**
     * Get HTML of the given course
     * @param string $course
     *
     * @return string
     * @throws CourseNotFoundException
     */
    private function getCourseHTML($course)
    {
        $http = $this->client->get(BASE_URL . DIRECTORY_SEPARATOR . 'course/' . $course, [
            'cookies' => $this->cookie,
            'verify' => false,
            'allow_redirects' => false
        ]);

        // TODO: handle 404 errors
        if ($http->getStatusCode() !== 200) {
            throw new CourseNotFoundException();
        }

        return $http->getBody()->getContents();
    }
}
