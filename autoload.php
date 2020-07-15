<?php
include_once __DIR__ . '/vendor/autoload.php';

$classLoader = new \Composer\Autoload\ClassLoader();
$classLoader->add("Commands", __DIR__ . '/Commands', true);
$classLoader->add("Exceptions", __DIR__ . '/Exceptions', true);
$classLoader->register();