<?php

use GuzzleHttp\Client;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use App\Coursehunter\Downloader;
use App\Coursehunter\Scrapper;

$options = standardizeOptions();

$client = new Client(['base_url' => BASE_URL]);

$adapter = new Local(__DIR__.'/Downloads');

$filesystem = new FileSystem($adapter);

$basePath = rtrim(__DIR__, '\/');

if (is_null($options['scrap']))
    return new Downloader($client, $filesystem, $options, $basePath);

return new Scrapper($client, $filesystem, $options, $basePath);


/**
 * Get all arguments from command line and make it more readable
 *
 * @return array
 */
function standardizeOptions() {
    $arguments = 'c:';
    $arguments .= 'e:';
    $arguments .= 'p:';
    $arguments .= '';

    $longOptions = [
        'course:',
        'email:',
        'password:',
        'scrap'
    ];

    $options = getopt($arguments, $longOptions);

    return [
        'course' =>
            isset($options['c'])
                ? trim(str_replace(BASE_URL . '/course/', '', $options['c']), '/')
                : null,
        'email' => $options['e'] ?? null,
        'password' => $options['p'] ?? null,
        'scrap' => $options['scrap'] ?? null
    ];
}
