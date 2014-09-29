<?php

date_default_timezone_set('UTC');

$try = array(
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../../../autoload.php',
);

foreach($try as $path) {
    if (file_exists($path)) {
        $autoLoader = include $path;
        break;
    }
}

$autoLoader->addPsr4('Sabre\\BObject\\',__DIR__ . '/BObject');
