#!/usr/bin/env php

<?php
date_default_timezone_set('Asia/Shanghai');
define('KAWAII_ENV', getenv('KAWAII_ENV'));

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../src/Kawaii.php';

$httpConfig = __DIR__ . '/../config/http-app.php';
$httpApp = new kawaii\http\Application($httpConfig);

$config = __DIR__ . '/../config/server.php';
$httpApp->run($config);