<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/4/4
 * Time: 18:25
 */

namespace app\process;


use kawaii\server\BaseProcess;
use Swoole\Process;

/**
 * Class Ping
 * @package app\process
 */
class Ping extends BaseProcess
{
    /**
     * @param Process $process
     */
    public function handle(Process $process)
    {
        while (true) {
            $time = microtime(true);
            $this->pushAll($time);
            usleep(1500000);
        }
    }

    private function pushAll(string $message)
    {
        foreach ($this->server->connections as $fd) {
            $connection = $this->server->getSwoole()->getConnection($fd);
            if ($connection->isWebSocket()) {
                var_dump($this->server->getSwoole()->getConnection($fd));
                $data = $connection->getParam('username', 'I am chendong') . ' - ' . $message;
                $this->server->getSwoole()->push($connection->fd, $data);
            }
        }
    }
}