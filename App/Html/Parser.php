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
}