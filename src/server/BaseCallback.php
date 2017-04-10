<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/4/10
 * Time: 17:24
 */

namespace kawaii\server;


use kawaii\base\BaseTask;
use kawaii\base\Object;
use Swoole\Server;

/**
 * Class BaseCallback
 * @package kawaii\server
 */
class BaseCallback extends Object
{
    /**
     * @var BaseServer
     */
    protected $server;

    /**
     * BaseCallback constructor.
     * @param BaseServer $server
     * @param array $config
     */
    public function __construct(BaseServer $server, array $config = [])
    {
        parent::__construct($config);
        $this->server = $server;
    }

    /**
     * @param Server $server
     */
    public function onMasterStart(Server $server): void
    {
        BaseServer::setProcessName('master process');
        echo "Master pid: {$server->master_pid} starting...\n";
    }

    /**
     * @param Server $server
     */
    public function onMasterStop(Server $server): void
    {
        echo "Master pid: {$server->master_pid} shutdown...\n";
    }

    /**
     * @param Server $server
     */
    public function onManagerStart(Server $server): void
    {
        BaseServer::setProcessName('manager');

        echo "Manager pid: {$server->manager_pid} starting...\n";
    }

    /**
     * @param Server $server
     */
    public function onManagerStop(Server $server): void
    {
        echo "Manager pid: {$server->manager_pid} stopped...\n";
    }

    /**
     * @param Server $server
     * @param int $workId
     */
    public function onWorkerStart(Server $server, int $workId): void
    {
        BaseServer::setProcessName($server->taskworker ? 'task' : 'worker');

        // @todo 需要重新载入配置

        echo ($server->taskworker ? 'task' : 'worker') . ": $workId starting...\n";
    }

    /**
     * @param Server $server
     * @param int $workId
     */
    public function onWorkerStop(Server $server, int $workId): void
    {
        echo "Worker: $workId stopped...\n";
    }

    /**
     * @param Server $server
     * @param int $workerId
     * @param int $workerPid
     * @param int $exitCode
     * @param int $signal
     */
    public function onWorkerError(
        Server $server,
        int $workerId,
        int $workerPid,
        int $exitCode,
        int $signal
    ): void {
        echo "Worker error: id {$workerId}, pid {$workerPid}, exit code {$exitCode}, signal: {$signal}.\n";
    }

    /**
     * @param Server $server
     * @param int $taskId
     * @param int $fromWorkerId
     * @param $data
     */
    public function onTask(Server $server, int $taskId, int $fromWorkerId, $data): void
    {
        if ($data instanceof BaseTask) {
            $data->handle($server, $taskId);
        }

        echo "Task {$taskId} starting, worker {$fromWorkerId}.\n";
    }

    /**
     * @param Server $server
     * @param int $taskId
     * @param $data
     */
    public function onFinish(Server $server, int $taskId, $data): void
    {
        if ($data instanceof BaseTask) {
            $data->done();
        }

        echo "Task {$taskId} run finished.\n";
    }

    /**
     * @param Server $server
     * @param int $fromWorkerId
     * @param $message
     */
    public function onPipeMessage(Server $server, int $fromWorkerId, $message): void
    {
        echo "Receive message: {$message} from worker {$fromWorkerId}.\n";
    }

    /**
     * bind callback
     */
    public function bind()
    {
        $this->server->on('Start', [$this, 'onMasterStart']);
        $this->server->on('Shutdown', [$this, 'onMasterStop']);
        $this->server->on('ManagerStart', [$this, 'onManagerStart']);
        $this->server->on('ManagerStop', [$this, 'onManagerStop']);
        $this->server->on('WorkerStart', [$this, 'onWorkerStart']);
        $this->server->on('WorkerStop', [$this, 'onWorkerStop']);
        $this->server->on('WorkerError', [$this, 'onWorkerError']);
        $this->server->on('PipeMessage', [$this, 'onPipeMessage']);
        $this->server->on('Task', [$this, 'onTask']);
        $this->server->on('Finish', [$this, 'onFinish']);
    }
}