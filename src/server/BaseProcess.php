<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/4/4
 * Time: 17:25
 */

namespace kawaii\server;


use kawaii\base\Object;
use Swoole\Process;

/**
 * Class BaseProcess
 * @package kawaii\server
 */
abstract class BaseProcess extends Object
{
    /**
     * @var BaseServer
     */
    protected $server;

    /**
     * @param BaseServer $server
     * @param bool $redirect
     * @param bool $createPipe
     * @return bool
     */
    public function run(BaseServer $server, bool $redirect = false, bool $createPipe = true): bool
    {
        $this->server = $server;
        $process = $this->createProcess($redirect, $createPipe);
        return $this->server->getSwoole()->addProcess($process);
    }

    /**
     * @param bool $redirect
     * @param bool $createPipe
     * @return Process
     */
    protected function createProcess(bool $redirect = false, bool $createPipe = true): Process
    {
        return new Process([$this, 'handle'], $redirect, $createPipe);
    }

    abstract public function handle(Process $process);
}