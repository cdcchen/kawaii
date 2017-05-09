#!/usr/bin/env php

<?php
date_default_timezone_set('Asia/Shanghai');
define('KAWAII_ENV', getenv('KAWAII_ENV'));

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../src/Kawaii.php';

$httpConfig = __DIR__ . '/../config/http-app.php';
$httpApp = new kawaii\http\Application($httpConfig);

$wsConfig = __DIR__ . '/../config/ws-app.php';
$wsApp = new kawaii\websocket\Application($wsConfig);

//$app->hook(function (\kawaii\web\Context $context, callable $next) {
////    var_dump($next);
//    $context = $next($context);
//    $context->response->getBody()->write('HOOK #1<br />BEFORE');
//
////    echo '---------------------' . PHP_EOL;
////    var_dump((string)$response->getBody());
////    echo '---------------------' . PHP_EOL;
//    $context->response->getBody()->write(__FILE__);
//    $context->response->getBody()->write('AFTER<br />');
////    echo '-----------111111----------' . PHP_EOL;
////    var_dump((string)$response->getBody());
////    echo '------------111111---------' . PHP_EOL;
//
//    return $context;
//});
//$app->get('/user/', function (\kawaii\web\Context $context, $next) {
//    /* @var \kawaii\web\Context $context */
//    $context = $next($context);
//    $context->response->getBody()->write('ROUTER #1 <br />');
//    return $context;
//});
//
//$app->get('/user/{uid:\d+}/post/{pid:\d+}',
//    function (\kawaii\web\Context $context, $next) {
//        /* @var \kawaii\web\Context $context */
//        $context = $next($context);
//        $context->response->getBody()->write(var_export($context->routeParams, true));
//        $context->response->getBody()->write('ROUTER #2 <br />');
//        return $context;
//    });
//$app->get('site/user/{uid:\w+}',
//    function (\kawaii\web\Context $context, $next) {
//        /* @var \kawaii\web\Context $context */
//        $context = $next($context);
//        $context->response->getBody()->write(var_export($context->routeParams, true));
//        $context->response->getBody()->write('<br />ROUTER #3 <br />');
//        return $context;
//    });
//$app->get('/', function (\kawaii\web\Context $context, $next) {
//    /* @var \kawaii\web\Context $context */
//    $context = $next($context);
//    $context->response->getBody()->write('Hello world!!!!!!');
//    return $context;
//});

$config = __DIR__ . '/../config/server.php';
$wsApp->http($httpApp)->run($config);