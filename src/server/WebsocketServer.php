<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/3/24
 * Time: 15:03
 */

namespace kawaii\server;


use Swoole\Server as SwooleServer;

class WebsocketServer extends Base
{

    protected function bindCallback(): void
    {
        // TODO: Implement bindCallback() method.
    }

    static protected function createSwooleServer(Listener $listener): SwooleServer
    {
        // TODO: Implement createSwooleServer() method.
    }
}