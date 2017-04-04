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
        foreach ($this->server->getSwoole()->connections as $fd) {
            $this->server->getSwoole()->push($fd, microtime(true));
        }
        sleep(1);
    }
}