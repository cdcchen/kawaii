<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/4/26
 * Time: 14:52
 */

namespace kawaii\server;


use Swoole\Server;

interface WorkerHookInterface
{
    /**
     * @param Server|HttpServer $server
     * @param int $workerId
     */
    public function run(Server $server, int $workerId);
}