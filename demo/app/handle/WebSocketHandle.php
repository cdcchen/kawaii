<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/4/9
 * Time: 21:01
 */

namespace app\handle;


use kawaii\base\Object;
use kawaii\server\WebSocketHandleInterface;
use kawaii\server\WebSocketMessageInterface;
use Psr\Http\Message\ServerRequestInterface;
use Swoole\WebSocket\Server;

class WebSocketHandle extends Object implements WebSocketHandleInterface
{

    public function handleOpen(ServerRequestInterface $req, Server $server): void
    {
        // TODO: Implement handleOpen() method.
    }

    public function handleMessage(WebSocketMessageInterface $message, Server $server)
    {
        echo "This is handle handleMessage.\n";
        // TODO: Implement handleMessage() method.
    }

    public function handleClose(Server $server, int $fd): void
    {
        // TODO: Implement handleClose() method.
    }
}