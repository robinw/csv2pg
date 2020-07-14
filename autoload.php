<?php
include_once __DIR__ . '/vendor/autoload.php';

$classLoader = new \Composer\Autoload\ClassLoader();
$classLoader->add("Commands", __DIR__ . '/Commands', true);
$classLoader->add("Models", __DIR__ . '/Models', true);
$classLoader->add("Fields", __DIR__ . '/Models/Fields', true);
$classLoader->register();