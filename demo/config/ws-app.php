<?php

/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/3
 * Time: 20:37
 */

return [
    'id' => 'kawaii',
    'basePath' => dirname(__DIR__),
    'version' => '1.0.0',
    'name' => 'My first kawaii project',
    'charset' => 'utf-8',
    'language' => 'zh-CN',
    'sourceLanguage' => 'zh-CN',
    'timeZone' => 'Asia/Shanghai',
    'controllerNamespace' => 'app\\controllers',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'staticPath' => [
        __DIR__ . '/../public'
    ],

    'components' => include(__DIR__ . '/components.php'),
    'routes' => include(__DIR__ . '/ws-routes.php'),
    'params' => include(__DIR__ . '/params.php'),
];