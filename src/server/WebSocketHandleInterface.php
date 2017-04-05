<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/4/1
 * Time: 16:48
 */

namespace kawaii\server;


use Swoole\Http\Request;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

/**
 * Interface WebSocketHandleInterface
 * @package kawaii\server
 */
interface WebSocketHandleInterface
{
    /**
     * @param Server $server
     * @param Request $request
     * @return mixed
     */
    public function handleOpen(Server $server, Request $request);

    /**
     * @param Server $server
     * @param Frame $frame
     * @return mixed
     */
    public function handleMessage(Server $server, Frame $frame);
}