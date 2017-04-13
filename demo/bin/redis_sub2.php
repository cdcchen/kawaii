<?php

//ini_set('default_socket_timeout', -1);

$client = new Swoole\Redis();
$client->on('Message', function (Swoole\Redis $redis, array $message) {
    print_r($message);
});

$client->connect('192.168.11.22', 6379, function (Swoole\Redis $client, $result) {
    $client->subscribe('example');

    if ($result === false) {
        echo "Connect failed.\n";
        return;
    } else {
        echo "Connected successfully.\n";
    }
});
