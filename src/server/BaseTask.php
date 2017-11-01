<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/23
 * Time: 09:34
 */

namespace kawaii\server;


use Swoole\Server;

/**
 * Class BaseTask
 * @package kawaii\base
 */
abstract class BaseTask
{
    /**
     * @var Server
     */
    private $server;
    /**
     * @var mixed
     */
    private $data;
    /**
     * @var int
     */
    private $taskId;
    /**
     * @var mixed
     */
    private $result;

    /**
     * @param mixed $data
     * @return mixed
     */
    public function setData($data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return int
     */
    public function getTaskId(): int
    {
        return $this->taskId;
    }

    /**
     * @param Server $server
     * @param int $taskId
     */
    public function handle(Server $server, int $taskId)
    {
        $this->server = $server;
        $this->taskId = $taskId;

        $this->result = $this->onTasking($this->data);
        $this->server->finish($this);
    }

    /**
     * execute when $this->finish() executed
     */
    public function done(): void
    {
        $this->onDone($this->result);
    }

    /**
     * @param mixed $result
     */
    protected function onDone($result): void
    {
    }

    /**
     * @param mixed $data
     * @return mixed
     */
    abstract protected function onTasking($data);
}