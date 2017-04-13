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
        var_dump(spl_object_hash($this->server));
        $redis = new \Redis();
        $redis->connect('192.168.11.22');
        $redis->subscribe(['example'], function ($redis, $channel, $message) {
            $text = "<?php\n" . var_export($message, true);
            $this->pushAll(highlight_string($text, true));
        });

        return;
    }

    private function pushAll(string $message)
    {
        foreach ($this->server->connections as $fd) {
            $conn = $this->server->getConnection($fd);
            if ($conn->isWebSocket()) {
                $this->server->getSwoole()->push($fd, $message);
            }
        }
    }
}