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
 *
 * @property int pid
 */
abstract class BaseProcess extends Object
{
    /**
     * @var BaseServer
     */
    protected $server;
    /**
     * @var Process
     */
    protected $process;

    /**
     * @param bool $redirect
     * @param bool $createPipe
     * @param string|null $name
     * @param array $config
     */
    public function __construct(
        bool $redirect = false,
        bool $createPipe = true,
        string $name = null,
        array $config = []
    ) {
        parent::__construct($config);

        $this->process = $this->createProcess($redirect, $createPipe);
        if (!empty($name)) {
            $this->process->name($name);
        }
        $this->prepare();
    }

    /**
     * @param BaseServer $server
     * @return bool Run process
     */
    public function run(BaseServer $server)
    {
        $this->server = $server;
        return $this->server->getSwoole()->addProcess($this->process);
    }

    /**
     * @return int
     */
    public function start(): int
    {
        return $this->process->start();
    }

    /**
     * @param int $status
     * @return int
     */
    public function exit(int $status = 0): int
    {
        return $this->process->exit($status);
    }

    /**
     * @return int
     */
    public function getPid(): int
    {
        return posix_getpid();
    }

    /**
     * before run
     */
    protected function prepare(): void
    {
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

    /**
     * @param Process $process
     */
    abstract public function handle(Process $process);
}