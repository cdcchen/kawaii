<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/11
 * Time: 21:03
 */

$serv = new \Swoole\WebSocket\Server("localhost", 9503);

$serv->on('Open', function(\Swoole\WebSocket\Server $server, $req) {
    echo "connection open: ".$req->fd;
    var_dump($server->connection_info($req->fd));
});

$serv->on('Message', function(\Swoole\WebSocket\Server $server, $frame) {
    echo "message: ".$frame->data;
    $server->push($frame->fd, json_encode(["hello", "world"]));
    $server->tick(1000, function () use ($server, $frame) {
        $server->push($frame->fd, json_encode(["hello", "world"]));
    });
});

$serv->on('Close', function(\Swoole\WebSocket\Server $server, $fd) {
    echo "connection close: ".$fd;
});

$serv->start();