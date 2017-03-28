#!/usr/bin/env php

<?php

date_default_timezone_set('Asia/Shanghai');
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../src/Kawaii.php';

$config = __DIR__ . '/../config/app.php';
$app = new kawaii\web\Application($config);

$config = __DIR__ . '/../config/server.php';
$server = new \kawaii\server\SocketServer('127.0.0.1', 9512);
$server->run($app);
