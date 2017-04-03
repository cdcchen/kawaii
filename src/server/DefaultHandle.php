<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/4/3
 * Time: 17:27
 */

namespace kawaii\server;


use kawaii\base\BaseTask;
use kawaii\base\Object;
use Swoole\Server as SwooleServer;

class DefaultHandle extends Object
{
    /**
     * @var BaseServer
     */
    protected $server;

    public function __construct(BaseServer $server, array $config = [])
    {
        parent::__construct($config);
        $this->server = $server;
    }

    public function onMasterStart(SwooleServer $server): void
    {
        BaseServer::setProcessName('master process');
        echo "Master pid: {$server->master_pid} starting...\n";
    }

    public function onMasterStop(SwooleServer $server): void
    {
        echo "Master pid: {$server->master_pid} shutdown...\n";
    }

    public function onManagerStart(SwooleServer $server): void
    {
        BaseServer::setProcessName('manager');

        echo "Manager pid: {$server->manager_pid} starting...\n";
    }

    public function onManagerStop(SwooleServer $server): void
    {
        echo "Manager pid: {$server->manager_pid} stopped...\n";
    }

    public function onWorkerStart(SwooleServer $server, int $workId): void
    {
        BaseServer::setProcessName($server->taskworker ? 'task' : 'worker');

        // @todo 需要重新载入配置

        echo ($server->taskworker ? 'task' : 'worker') . ": $workId starting...\n";
    }

    public function onWorkerStop(SwooleServer $server, int $workId): void
    {
        echo "Worker: $workId stopped...\n";
    }

    public function onWorkerError(
        SwooleServer $server,
        int $workerId,
        int $workerPid,
        int $exitCode,
        int $signal
    ): void {
        echo "Worker error: id {$workerId}, pid {$workerPid}, exit code {$exitCode}, signal: {$signal}.\n";
    }

    public function onConnect(SwooleServer $server, int $fd, int $reactorId): void
    {
        echo "Client {$fd} form reactor {$reactorId} connected.\n";
    }

    public function onClose(SwooleServer $server, int $fd, int $reactorId): void
    {
        echo "Client {$fd} from reactor {$reactorId} disconnected.\n";
    }

    public function onTask(SwooleServer $server, int $taskId, int $fromWorkerId, $data): void
    {
        if ($data instanceof BaseTask) {
            $data->handle($server, $taskId);
        }

        echo "Task {$taskId} starting, worker {$fromWorkerId}.\n";
    }

    public function onFinish(SwooleServer $server, int $taskId, $data): void
    {
        if ($data instanceof BaseTask) {
            $data->done();
        }

        echo "Task {$taskId} run finished.\n";
    }

    public function onPipeMessage(SwooleServer $server, int $fromWorkerId, $message): void
    {
        echo "Receive message: {$message} from worker {$fromWorkerId}.\n";
    }

    public function onReceive(SwooleServer $server, int $fd, int $fromWorkerId, string $data): void
    {
        echo "Receive data: {$data} from client {$fd}, worker {$fromWorkerId}.\n";
    }

    public function onPacket(SwooleServer $server, string $data, array $client): void
    {
        $fd = unpack('L', pack('N', ip2long($client['address'])))[1];
        $fromId = ($client['server_socket'] << 16) + $client['port'];
        $server->send($fd, "I had received data: {$data}", $fromId);

        echo "Receive UDP data: {$data} from {$fd}.\n";
    }
}