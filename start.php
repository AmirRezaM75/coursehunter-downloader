<?php

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


$downloader = require_once __DIR__.'/bootstrap.php';

try {
    $downloader->start();
} catch (Exception $exception) {
    echo $exception->getMessage();
}