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


$response = new \kawaii\http\Response();

$start = microtime(1);
for ($i=0; $i<100000; $i++) {
    new \kawaii\http\Response();
}
$time = microtime(1) - $start;
echo "Time: $time\n\n";




$start = microtime(1);
for ($i=0; $i<100000; $i++) {
    clone $response;
}
$time = microtime(1) - $start;
echo "Time: $time\n\n";


$start = microtime(1);
for ($i=0; $i<100000; $i++) {
    array(__FILE__);
}
$time = microtime(1) - $start;
echo "Time: $time\n\n";