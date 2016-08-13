<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/12
 * Time: 10:49
 */

$client = new \Swoole\Redis();

$client->on('Message', function (\Swoole\Redis $client, $message) {
    var_dump($message);
    var_dump(json_decode($message[2], true));
});

$client->connect('192.168.11.22', 6379, function (\Swoole\Redis $client, $result) {
    if ($result === false) {
        echo 'Connect to redis server failed.', PHP_EOL;
        return ;
    }

    $client->subscribe('monitor');
});