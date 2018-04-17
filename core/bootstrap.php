<?php

require(__DIR__ . '/autoload.php');
require(dirname(__DIR__) . '/vendor/autoload.php');

$loader = new SplClassLoader( 'core');
$loader->register();