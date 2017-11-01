<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/11/1
 * Time: 14:57
 */

$http = new swoole_websocket_server("localhost", 9501);
$http->set([
    'open_websocket_protocol' => true,
]);

$http->on('message', function (swoole_websocket_server $server, $frame) {
    echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
    $server->push($frame->fd, "this is server");
});

$http->on('request', function ($request, $response) {
    $response->end("<h1>Hello Swoole. #".rand(1000, 9999)."</h1>");
});
$http->start();