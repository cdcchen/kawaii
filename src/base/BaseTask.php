<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/23
 * Time: 09:34
 */

namespace kawaii\base;


use Swoole\Server;

/**
 * Class BaseTask
 * @package kawaii\base
 *
 * @property mixed $data
 * @property int $taskId
 */
abstract class BaseTask extends Object
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
    public function setData($data)
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
    public function getTaskId()
    {
        return $this->taskId;
    }

    /**
     * @param Server $server
     * @param int $taskId
     */
    public function handle(Server $server, $taskId)
    {
        $this->server = $server;
        $this->taskId = $taskId;

        $this->result = $this->onTasking($this->data);
        $this->server->finish($this);
    }

    /**
     * execute when $this->finish() executed
     */
    public function done()
    {
        $this->onDone($this->result);
    }

    /**
     * @param mixed $result
     */
    protected function onDone($result)
    {
    }

    /**
     * @param mixed $data
     * @return mixed
     */
    abstract protected function onTasking($data);
}