<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/4/10
 * Time: 17:24
 */

namespace kawaii\server;


use kawaii\base\BaseTask;
use Swoole\Redis;
use Swoole\Server;

/**
 * Class BaseCallback
 * @package kawaii\server
 */
abstract class BaseCallback
{
    /**
     * @param Server|ServerTrait $server
     */
    public function onMasterStart(Server $server): void
    {
        $server->setProcessName('master process');
        echo "Master pid: {$server->master_pid} starting...\n";


        $redis = new Redis();
        $redis->on('Message', function (Redis $redis, $result) use ($server) {
            $text = "<?php\n" . var_export($result, true);
            foreach ($server->connections as $fd) {
                $connection = $server->getConnection($fd);
                if ($connection->isWebSocket()) {
                    $server->push($fd, highlight_string($text, true));
                }
            }
        });
        $redis->connect('127.0.0.1', 6379, function (Redis $redis, $result) {
            var_dump($result);
            $redis->subscribe('example');
        });
    }

    /**
     * @param Server $server
     */
    public function onMasterStop(Server $server): void
    {
        echo "Master pid: {$server->master_pid} shutdown...\n";
    }

    /**
     * @param Server|ServerTrait $server
     */
    public function onManagerStart(Server $server): void
    {
        $server->setProcessName('manager');

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
     * @param Server|ServerTrait $server
     * @param int $workId
     */
    public function onWorkerStart(Server $server, int $workId): void
    {
        $server->setProcessName($server->taskworker ? 'task' : 'worker');

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
     * @param Server $server
     */
    public function bind(Server $server): void
    {
        $server->on('Start', [$this, 'onMasterStart']);
        $server->on('Shutdown', [$this, 'onMasterStop']);
        $server->on('ManagerStart', [$this, 'onManagerStart']);
        $server->on('ManagerStop', [$this, 'onManagerStop']);
        $server->on('WorkerStart', [$this, 'onWorkerStart']);
        $server->on('WorkerStop', [$this, 'onWorkerStop']);
        $server->on('WorkerError', [$this, 'onWorkerError']);
        $server->on('PipeMessage', [$this, 'onPipeMessage']);
        $server->on('Task', [$this, 'onTask']);
        $server->on('Finish', [$this, 'onFinish']);
    }
}