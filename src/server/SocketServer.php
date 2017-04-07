<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/3/30
 * Time: 13:20
 */

namespace kawaii\server;


use Swoole\Server as SwooleServer;

/**
 * Class SocketServer
 * @package kawaii\server
 */
class SocketServer extends BaseServer
{
    /**
     * @param Listener $listener
     * @return SwooleServer
     */
    protected static function swooleServer(Listener $listener): SwooleServer
    {
        return new SwooleServer($listener->host, $listener->port, $listener->type, $listener->mode);
    }
}