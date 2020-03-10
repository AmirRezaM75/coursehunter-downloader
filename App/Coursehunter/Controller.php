<?php

namespace App\Coursehunter;

use App\Exceptions\CourseNotFoundException;
use App\Html\Parser;
use GuzzleHttp\Client;

class Controller
{

    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function courseEpisodes($course)
    {
        $html = $this->getCourseHTML($course);

        $array[$course] = Parser::getEpisodesArray($html);

        return $array;
    }

    public function getCourseHTML($course)
    {
        $http = $this->client->get(BASE_URL . DIRECTORY_SEPARATOR . 'course/' . $course);

        // TODO: handle 404 errors
        if ($http->getStatusCode() !== 200) {
            throw new CourseNotFoundException();
        }

        return $http->getBody()->getContents();
    }

}