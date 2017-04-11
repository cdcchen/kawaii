<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/4/10
 * Time: 16:20
 */

namespace kawaii\server;


use Swoole\Server as SwooleServer;

interface SocketHandleInterface
{
    /**
     * @param SwooleServer $server
     * @param int $fd
     * @param int $fromWorkerId
     * @param string $data
     */
    public function handleReceive(SwooleServer $server, int $fd, int $fromWorkerId, string $data): void;
}