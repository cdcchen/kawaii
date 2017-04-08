<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/4/1
 * Time: 16:48
 */

namespace kawaii\server;


use Psr\Http\Message\ServerRequestInterface;
use Swoole\WebSocket\Server;

/**
 * Interface WebSocketHandleInterface
 * @package kawaii\server
 */
interface WebSocketHandleInterface
{
    /**
     * @param ServerRequestInterface $req
     * @param Server $server
     */
    public function handleOpen(ServerRequestInterface $req, Server $server): void;

    /**
     * @param WebSocketMessageInterface $message
     * @param Server $server
     */
    public function handleMessage(WebSocketMessageInterface $message, Server $server): void;

    /**
     * @param Server $server
     * @param int $fd
     */
    public function handleClose(Server $server, int $fd): void;
}