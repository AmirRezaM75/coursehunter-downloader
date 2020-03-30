<?php

namespace App\Html;

use Symfony\Component\DomCrawler\Crawler;

class Parser
{
    public static function getEpisodesArray($html)
    {
        $parser = new Crawler($html);

        $node = $parser->filter(".lessons-item");

        return $series = $node->each(function(Crawler $crawler, $index) {
            $name = $crawler->filter('.lessons-name')->text();
            $link = $crawler->filter("link[itemprop='url']")->attr('href');

            return [
                'number' => intval($index) + 1,
                'name' => trim($name),
                'link' => $link,
            ];
        });
    }

    public static function getBasicInformation($html)
    {
        $parser = new Crawler($html);

        $info = [];

        if ($parser->filter(".lessons-item")->count() > 0)
            $info['number'] = $parser->filter(".lessons-item")->count();

        if ($parser->filter(".lessons-item link[itemprop='url']")->count() > 0)
            $info['link'] = $parser->filter(".lessons-item link[itemprop='url']")->attr('href');

        if ($parser->filter('.course-wrap-bottom span a')->eq(0)->count() > 0)
            $info['package'] = $parser->filter('.course-wrap-bottom span a')->eq(0)->attr('href');

        if ($parser->filter('.course-wrap-bottom span a')->eq(1)->count() > 0)
            $info['material'] = $parser->filter('.course-wrap-bottom span a')->eq(1)->attr('href');

        return $info;
    }


    /**
     * Get the last page number of coursehunter archive page
     *
     * @param string $html
     * @return int
    */
    public static function getLastPage($html)
    {
        $parser = new Crawler($html);

        return intval(trim($parser->filter("ul.pagination__ul li:nth-last-child(2) a span")->text()));
    }

    public static function getCourseNamesURL($html)
    {
        $parser = new Crawler($html);

        $node = $parser->filter(".course-details-bottom a");

        return $node->each(function(Crawler $crawler){
            return trim(str_replace(BASE_URL . '/course/', '', $crawler->attr('href')), '/');
        });
    }
}
