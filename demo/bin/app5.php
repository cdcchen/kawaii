#!/usr/bin/env php

<?php
date_default_timezone_set('Asia/Shanghai');
define('KAWAII_ENV', getenv('KAWAII_ENV'));

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../src/Kawaii.php';

$config = __DIR__ . '/../config/http-app.php';
$app = new kawaii\http\Application($config);

$config = require(__DIR__ . '/../config/server.php');
$app->run($config);