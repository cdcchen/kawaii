<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/4/1
 * Time: 16:27
 */

namespace kawaii\websocket;


use Kawaii;
use kawaii\server\WebSocketHandleInterface;
use kawaii\server\WebSocketMessageInterface;
use Psr\Http\Message\ServerRequestInterface;
use Swoole\WebSocket\Server;


/**
 * Class Application
 * @package kawaii\websocket
 *
 * @property WebSocketHandleInterface $handle
 */
class Application extends \kawaii\web\Application implements WebSocketHandleInterface
{
    /**
     * @param ServerRequestInterface $req
     * @param Server $server
     */
    public function handleOpen(ServerRequestInterface $req, Server $server): void
    {
        // TODO: Implement handleOpen() method.
    }

    /**
     * @param WebSocketMessageInterface|Message $message
     * @param Server $server
     */
    public function handleMessage(WebSocketMessageInterface $message, Server $server): void
    {
        echo "This is app handleMessage.\n";
    }

    /**
     * @param Server $server
     * @param int $fd
     */
    public function handleClose(Server $server, int $fd): void
    {
        // TODO: Implement handleClose() method.
    }
}