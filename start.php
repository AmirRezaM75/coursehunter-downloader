<?php

define('BASE_URL', 'https://coursehunter.net');

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
| Composer provides a convenient, automatically generated class loader for
| our application. We just need to require it into the script here so
| we don't have to worry about manual loading any of our classes.
|
*/

require __DIR__.'/vendor/autoload.php';


/*
|--------------------------------------------------------------------------
| Application Factory
|--------------------------------------------------------------------------
| We use Downloader or Scrapper class based on your console arguments
|
*/

$application = require_once __DIR__.'/app.php';

try {
    $application->start();
} catch (Exception $exception) {
    echo $exception->getMessage() . PHP_EOL;
}
