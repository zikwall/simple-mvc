<?php session_start();

require_once __DIR__.'/core/bootstrap.php';
require_once __DIR__.'/core/Core.php';

$config = \core\helpers\ArrayHelper::merge(
    require(__DIR__ . '/app/configs/common.php'),
    require(__DIR__ . '/app/configs/web.php')
);

(new \core\web\Application($config))->run();



