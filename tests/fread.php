<?php

/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/7/24
 * Time: 13:49
 */
$filename = __DIR__ . '/app.php';

$fp = fopen($filename, 'r');
//fseek($fp, 10);
//$content = fread($fp, 100);
$content1 = stream_get_contents($fp);
fseek($fp, 0);
$content2 = stream_get_contents($fp);
fclose($fp);

echo '--------------------------------------', PHP_EOL;
var_dump($content1);
echo '--------------------------------------', PHP_EOL;
var_dump($content2);
echo '--------------------------------------', PHP_EOL;