#!/usr/bin/env php

<?php

date_default_timezone_set('Asia/Shanghai');
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../src/Kawaii.php';

$config = __DIR__ . '/../config/app.php';
$app = new kawaii\http\Application($config);

$config = __DIR__ . '/../config/websocket.php';
$app2 = new \kawaii\websocket\Application($config);

$config = __DIR__ . '/../config/server.php';
$server = new \kawaii\server\WebSocketServer($config);
$ping = new \app\process\Ping();
$publish = new \app\process\Publish();
$server->addProcess($ping);
//$server->addProcess($publish);

$count = 11;

$server->onStarted = function (\kawaii\server\BaseServer $server) {
    $client = new swoole_redis();
    $client->on('Message', function (swoole_redis $redis, array $message) use ($server) {
        $text = "<?php\n" . var_export($message, true);

        foreach ($server->connections as $fd) {
            $connection = new \kawaii\server\Connection($fd, $server->getSwoole()->connection_info($fd));
            if ($connection->isWebSocket()) {
                $server->getSwoole()->push($connection->fd, highlight_string($text, true));
            }
        }
    });

    $client->connect('192.168.11.22', 6379, function (swoole_redis $client, $result) {
        $client->subscribe('example');

        if ($result === false) {
            echo "Connect failed.\n";
            return;
        } else {
            echo "Connected successfully.\n";
        }
    });
};
$server->run($app2, $app)
       ->start();
