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
     * @var mixed
     */
    private $data;
    /**
     * @var mixed
     */
    private $result;
    /**
     * @var bool
     */
    private $success;
    /**
     * @var Base
     */
    private $server;
    /**
     * @var int
     */
    private $taskId;

    /**
     * BaseTask constructor.
     * @param array $data
     * @param array $config
     */
    public function __construct($data, $config = [])
    {
        $this->data = $data;
        parent::__construct($config);
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return mixed mixed
     */
    public function getTaskId()
    {
        return $this->taskId;
    }

    /**
     * @param mixed $data
     */
    public function finish($data)
    {
        $this->result = $data;
        $this->server->finish($this);
    }

    /**
     * @param Base $server
     * @param int $taskId
     */
    public function handle(Server $server, $taskId)
    {
        $this->server = $server;
        $this->taskId = $taskId;

        $result = $this->onTask($this->data);
        if ($result !== null) {
            $this->finish($result);
        }
    }

    /**
     * The callback of task execute completed
     */
    public function completed()
    {
        if ($this->success === true) {
            $this->onSuccess($this->result);
        } elseif ($this->success === false) {
            $this->onFailed($this->result);
        }

        $this->onFinished($this->result);
    }


    /**
     * @param bool $flag
     * @return $this
     */
    protected function setSuccess($flag = true)
    {
        $this->success = (bool)$flag;
        return $this;
    }

    /**
     * @param mixed $result
     */
    protected function onSuccess($result)
    {
    }

    /**
     * @param mixed $result
     */
    protected function onFailed($result)
    {
    }


    /**
     * @param mixed $result
     */
    protected function onFinished($result)
    {
    }

    /**
     * @param mixed $data
     * @return mixed
     */
    abstract protected function onTask($data);
}