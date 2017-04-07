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
 * Class Publish
 * @package app\process
 */
class Publish extends BaseProcess
{
    /**
     * @param Process $process
     */
    public function handle(Process $process)
    {
        while (true) {
            $time = microtime(true);
            foreach ($this->server->connections as $fd) {
                $conn = $this->server->getConnection($fd);
                if ($conn->isWebSocket()) {
                    $this->server->getSwoole()->push($fd, $time);
                }
            }
            sleep(1);
        }
    }
}