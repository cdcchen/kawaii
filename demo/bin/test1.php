<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/15
 * Time: 14:44
 */
date_default_timezone_set('Asia/Shanghai');
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../src/Kawaii.php';

$data = <<<DATA
HTTP/1.1 200 OK

app\controllers\PostController::actionIndex
DATA;

$request = \kawaii\web\Request::create($data);

$start = microtime(1);
for ($i=0; $i<1000000; $i++) {
    new \kawaii\web\Context($request, new \kawaii\web\Response());
}
$time = microtime(1) - $start;
echo "Time: $time\n\n";




$dt = new \kawaii\web\Context($request, new \kawaii\web\Response());
$start = microtime(1);
for ($i=0; $i<1000000; $i++) {
    clone $dt;
}
$time = microtime(1) - $start;
echo "Time: $time\n\n";