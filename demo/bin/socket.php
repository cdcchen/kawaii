<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/11
 * Time: 21:03
 */

$server = new swoole_websocket_server("0.0.0.0", 9503);

$server->on('open', function (swoole_websocket_server $server, $request) {
    echo "server: handshake success with fd{$request->fd}\n";
});

$server->on('request', function ($request, $response) {
    $response->end(file_get_contents(__DIR__ . '/index.html'));
});

$server->on('message', function (swoole_websocket_server $server, $frame) {
    echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";

    $server->push($frame->fd, "this is server");
    swoole_timer_tick(1000, function ($timerId) use ($server, $frame) {
        $server->push($frame->fd, microtime(true));
    });
});

$server->on('close', function ($ser, $fd) {
    echo "client {$fd} closed\n";
});

$server->start();