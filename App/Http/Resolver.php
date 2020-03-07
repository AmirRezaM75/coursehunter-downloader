<?php


namespace App\Http;


use App\Utility\Utility;
use GuzzleHttp\Client;
use GuzzleHttp\Event\ProgressEvent;

class Resolver
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function download($episode, $courseName)
    {
        $number = sprintf("%03d", $episode['number']);

        $saveTo = 'Downloads/' . $courseName . DIRECTORY_SEPARATOR . $number . ' - ' . $episode['name'] . '.mp4';
        Utility::write(sprintf("Download started: %s ....", $episode['name']));
        $this->downloadFromURL($episode['link'], $saveTo);
    }

    private function downloadFromURL($url, $output) {
        while(true) {
            $downloadedBytes = file_exists($output) ? filesize($output) : 0;
            $headers = [
                'save_to' => fopen($output, 'a'),
                'verify' => false,
                'headers' => [
                    'Range' => 'bytes=' . $downloadedBytes . '-'
                ]
            ];

            $request = $this->client->createRequest('GET', $url, $headers);

            if (php_sapi_name() == "cli") { //on cli show progress
                $request->getEmitter()->on('progress', function (ProgressEvent $e) use ($downloadedBytes) {
                    printf("> Total: %d%% Downloaded: %s of %s     \r",
                        Utility::getPercentage($e->downloaded + $downloadedBytes, $e->downloadSize),
                        Utility::formatBytes($e->downloaded + $downloadedBytes),
                        Utility::formatBytes($e->downloadSize));
                });
            }

            $response = $this->client->send($request);

            return true;
        }
    }
}