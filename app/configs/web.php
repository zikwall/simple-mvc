<?php

return [
    'id' => 'simple',
    'name' => 'My MVC Application',
    'siteName' => 'PHP MVC Framework',
    'version' => '1.0',
    'basePath' => dirname(__DIR__),
    'components' => [
        'view' => ['class' => 'core\web\View'],
    ]
];