#!/usr/bin/env php

<?php

date_default_timezone_set('Asia/Shanghai');
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../src/Kawaii.php';

$config = __DIR__ . '/../config/app.php';
$app = new kawaii\web\Application($config);
$app->run();

$config = __DIR__ . '/../config/websocket.php';
$app2 = new \kawaii\websocket\Application($config);
$app2->run();

$config = __DIR__ . '/../config/server.php';
$server = new \kawaii\server\WebsocketServer($config);
$server->http($app)->run($app2)->start();
