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

    public function download($information)
    {
        $length = $information['courseItems'] !== 0 ? floor(log10($information['courseItems']) + 1) : 1;

        $number = sprintf("%0{$length}d", $information['episodeNumber']);

        $saveTo = 'Downloads/' . $information['courseName'] . DIRECTORY_SEPARATOR . $number . '.mp4';

        Utility::write(sprintf("Download started: %s / %s ....", $information['episodeNumber'], $information['courseItems']));

        $this->downloadFromURL($information['episodeLink'], $saveTo);
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
