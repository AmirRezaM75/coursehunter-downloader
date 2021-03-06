<?php

namespace App\Coursehunter;

use App\Exceptions\CourseNotFoundException;
use App\Exceptions\SubscriptionNotActiveException;
use App\Html\Parser;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

class Controller
{

    private $client;
    private $cookie;

    public function __construct(Client $client)
    {
        $this->client = $client;
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

    /** Get HTML of the given course
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