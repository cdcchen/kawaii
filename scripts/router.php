<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/1
 * Time: 10:35
 */
require __DIR__ . '/../vendor/autoload.php';

$router = new \kawaii\web\Router();
$router->addRoute('get', '/user/', function ($req, $res, $next) {
    echo __FUNCTION__;
});

$router->addRoute('get', '/user/{uid:\d+}/post/{pid:\d+}', function ($req, $res, $next) {
    echo __FUNCTION__;
});

$result = $router->dispatch('get', '/user/11/post/22');
print_r($result);

//print_r($router);